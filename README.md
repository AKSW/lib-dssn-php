# lib-dssn-php

## Introduction

A PHP library to create and consume Social Web activities as described
on [activitystrea.ms](http://activitystrea.ms) from and to RDF knowledge
bases and Atom feeds.

The library is a spin-off from [OntoWiki](http://ontowiki.net)s
Feature-DSSN branch and some factory methods use the [Erfurt
Semantic Web API](http://github.com/AKSW/Erfurt) to communicate
with the triple store (getFromStore). If you do not want to export
RDF, you can use it without any dependency.

## Features

Currently it supports the following tasks:

* create activities in memory
* export activities to RDF knowledge bases (currently using [ARC2](https://github.com/semsol/arc2) turtle templates)
* export activities to Atom entries / feeds (via [DOMDocument](http://php.net/manual/en/class.domdocument.php)
* load activities from a triple store via SPARQL (currently using [Erfurt](http://github.com/AKSW/Erfurt))

At the moment, the following activities (verbs) are supported. Please
refer [the verb registry](http://activitystrea.ms/registry/verbs/) for
further information.

* share
* post

At the moment, the following object are supported. Please refer [the
object type registry](http://activitystrea.ms/registry/object_types/)
for further information.

* note (status message)
* bookmark

## Roadmap

On the close roadmap are the following tasks:

* consume activities from Atom feed entries

## Code Example

Please have a look at the provided
[example.php](https://github.com/seebi/lib-dssn-php/blob/master/example.php).

## Warranty / License

This program is free software. It comes without any
warranty, to the extent permitted by applicable law. You
can redistribute it and/or modify it under the terms of
the [Do What The Fuck You Want To Public License, Version
2](http://sam.zoy.org/wtfpl/COPYING), as published by Sam Hocevar. See
[http://sam.zoy.org/wtfpl/COPYING](http://sam.zoy.org/wtfpl/COPYING) for
more details.

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                    Version 2, December 2004

    Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>

    Everyone is permitted to copy and distribute verbatim or modified
    copies of this license document, and changing it is allowed as long
    as the name is changed.

               DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
      TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

     0. You just DO WHAT THE FUCK YOU WANT TO.

