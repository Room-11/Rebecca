<?php

const VIDEO_URL = 'https://www.youtube.com/watch?v=kfVsfOSbJY0';
const DEFAULT_CONTENT_TYPE = "text/html";

$contentTypeHandlers = [
    "text/plain" => "rebeccaMe",
    "text/html" => "htmlRebeccaMe",
    "application/json" => "jsonRebeccaMe",
];

$method = isset($_SERVER["REQUEST_METHOD"])
    ? strtoupper($_SERVER["REQUEST_METHOD"])
    : null;

$contentTypes = isset($_SERVER["HTTP_ACCEPT"])
    ? array_map('trim', explode(",", $_SERVER["HTTP_ACCEPT"]))
    : DEFAULT_CONTENT_TYPE;

if ($method != "GET") {
    http_response_code(405);
    exit;
}

foreach ($contentTypes as $contentType) {
    if (isset($contentTypeHandlers[$contentType]) && is_callable($contentTypeHandlers[$contentType])) {
        header("Content-Type: $contentType");
        echo $contentTypeHandlers[$contentType]();
        exit();
    }
    header("Content-Type: text/html");
    echo htmlRebeccaMe();
}

function jsonRebeccaMe() {
    return json_encode(["data" => rebeccaMe()]);
}

function htmlRebeccaMe() {
    $data = rebeccaMe();
    if ($data === VIDEO_URL) {
        header('Location: ' . VIDEO_URL);
        return "<h1>It's Friday, Friday, gotta get down on Friday</h1>";
    }
    return '<html lang="en"><head><meta charset="UTF-8"><title>Rebecca says</title></head><body>' . $data . '</body></html>';
}

function rebeccaMe()
{
    switch (date('l')) {
        case 'Thursday':
            return "Happy Prebeccaday!";
        case 'Friday':
            return VIDEO_URL;
        case 'Saturday':
            return "Today is Saturday. And Sunday comes afterwards";
        default:
            return getCountdown();
    }
}

function getCountdown()
{
    $timeLeft = getTimeUntilNextFriday();

    return sprintf(
        'Only %d days, %d hours and %d minutes left until Rebeccaday, OMG!',
        $timeLeft->days,
        $timeLeft->h,
        $timeLeft->i
    );

}

function getTimeUntilNextFriday()
{
    $now = new \DateTime('now', new \DateTimeZone('UTC'));
    $friday = new \DateTime('next friday', new \DateTimeZone('UTC'));

    return $now->diff($friday);
}