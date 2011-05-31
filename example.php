<?php
// prepare a simple autoloading mechanism
include('DSSN/Utils.php');
DSSN_Utils::registerAutoload();

// create the bookmark object
$object = new DSSN_Activity_Object_Bookmark;
$object->setIri('http://github.com');
$object->setLabel('github');
$object->setThumbnail('http://cligs.websnapr.com/?size=t&url='.$object->getIri());

// create the actor object
$actor = new DSSN_Activity_Actor_User;
$actor->setIri('http://sebastian.tramp.name');
$actor->setName('Sebastian Tramp');

// create the share activity and add object and actor
$activity = new DSSN_Activity;
$activity->setVerb(new DSSN_Activity_Verb_Share);
$activity->setObject($object);
$activity->setActor($actor);

// create an activity feed of one activity entry
$feed = new DSSN_Activity_Feed();
$feed->setTitle('Example Feed');
$feed->setLinkSelf('http://example.org/my/feed.atom');
$feed->setLinkHtml('http://example.org/my/feed.html');
$feed->addActivity($activity);

// return the feed to the browse
//$feed->send();

// eat you own dog food: re-parse the feeds XML and output the activity titles
$newfeed = DSSN_Activity_Feed_Factory::newFromXml($feed->toXml());
foreach ($newfeed->getActivities() as $key => $activity) {
    echo $activity->getTitle() . PHP_EOL;
}

// load an external feed and display activity titles
$externalFeed = DSSN_Activity_Feed_Factory::newFromUrl('http://www.testmash.com/temp/ASms/PostNote.xml');
foreach ($externalFeed->getActivities() as $key => $activity) {
    echo $activity->getTitle() . PHP_EOL;
}

