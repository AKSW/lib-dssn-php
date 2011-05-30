<?php
/* prepare a simple autoloading mechanism */
include('DSSN/Utils.php');
DSSN_Utils::registerAutoload();

/* create the bookmark object */
$object = new DSSN_Activity_Object_Bookmark;
$object->setIri('http://github.com');
$object->setLabel('github');
$object->setThumbnail('http://cligs.websnapr.com/?size=t&url='.$object->getIri());

/* create the actor object */
$actor = new DSSN_Activity_Actor_User;
$actor->setIri('http://sebastian.tramp.name');
$actor->setName('Sebastian Tramp');

/* create the share activity and add object and actor */
$activity = new DSSN_Activity;
$activity->setVerb(new DSSN_Activity_Verb_Share);
$activity->setObject($object);
$activity->setActor($actor);

/* create an activity of one activity entry */
$feed = new DSSN_Activity_Feed();
$feed->setTitle('Example Feed');
$feed->setLinkSelf('http://example.org/my/feed.atom');
$feed->setLinkHtml('http://example.org/my/feed.html');
$feed->addActivity($activity);

/* return the feed to the browser */
$feed->send();

