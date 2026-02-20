<?php

require_once __DIR__ . '/../config.php';

function findUserByUsername($username)
{
    global $db;
    return $db->users->findOne(['username' => $username]);
}

function getActivePoll()
{
    global $db;
    return $db->polls->findOne(['status' => 'active']);
}

function getAvailablePolls()
{
    global $db;
    return $db->polls->find(['status' => 'active'], ['sort' => ['created_at' => -1]]);
}

function getPollById($pollId)
{
    global $db;
    return $db->polls->findOne(['_id' => $pollId]);
}

function getLatestPoll()
{
    global $db;
    return $db->polls->findOne([], ['sort' => ['created_at' => -1]]);
}

function getPollOptions($pollId)
{
    global $db;
    return $db->options->find(['poll_id' => $pollId]);
}

function hasUserVoted($pollId, $userId)
{
    global $db;
    return $db->votes->findOne(['poll_id' => $pollId, 'user_id' => $userId]) !== null;
}

function isOptionInPoll($pollId, $optionId)
{
    global $db;
    return $db->options->findOne(['_id' => $optionId, 'poll_id' => $pollId]) !== null;
}

function castVote($pollId, $optionId, $userId)
{
    global $db;

    if (hasUserVoted($pollId, $userId)) {
        return 'You have already voted in this poll.';
    }

    if (!isOptionInPoll($pollId, $optionId)) {
        return 'Invalid option for this poll.';
    }

    $db->votes->insertOne([
        'poll_id' => $pollId,
        'option_id' => $optionId,
        'user_id' => $userId,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);

    return 'Vote submitted successfully!';
}

function handleVoteRequest($pollIdValue, $optionIdValue, $sessionUserIdValue)
{
    $pollId = oid($pollIdValue);
    $optionId = oid($optionIdValue);
    $sessionUserIdValue = trim((string)$sessionUserIdValue);
    $userId = oid($sessionUserIdValue);
    if ($userId === null && $sessionUserIdValue !== '') {
        $userId = $sessionUserIdValue;
    }

    if ($pollId === null || $optionId === null || $userId === null) {
        return 'Invalid vote payload.';
    }

    return castVote($pollId, $optionId, $userId);
}

function createPoll($question, array $options)
{
    global $db;

    $pollInsert = $db->polls->insertOne([
        'question' => $question,
        'status' => 'active',
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);

    $pollId = $pollInsert->getInsertedId();

    foreach ($options as $option) {
        $db->options->insertOne([
            'poll_id' => $pollId,
            'option_text' => $option
        ]);
    }

    return $pollId;
}

function getPollResults($pollId)
{
    global $db;

    $results = [];
    $options = getPollOptions($pollId);

    foreach ($options as $option) {
        $results[] = [
            'option_text' => (string)($option['option_text'] ?? ''),
            'total' => $db->votes->countDocuments(['option_id' => $option['_id']])
        ];
    }

    return $results;
}
