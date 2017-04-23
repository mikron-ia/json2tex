# JSON to TeX maker
[![Build Status](https://travis-ci.org/mikron-ia/json2tex.svg?branch=master)](https://travis-ci.org/mikron-ia/json2tex)
[![Code Climate](https://codeclimate.com/github/mikron-ia/json2tex/badges/gpa.svg)](https://codeclimate.com/github/mikron-ia/json2tex)
[![Test Coverage](https://codeclimate.com/github/mikron-ia/json2tex/badges/coverage.svg)](https://codeclimate.com/github/mikron-ia/json2tex/coverage)

**Please note:** this project was something else before, a JSON to DOC converter. I realised this makes very little sense, thus altered its purpose before more could be written.

## Background
Some resources - in this case, game data - are better stored in simple format of JSON or XML and subsequently processed into more complex formats, like DB records or DOC file containing a game manual. This project is intended to present the case with building a document from JSON. 

Desired functionalities:

- Ability to write down a JSON-described text with some respect to hierarchy
- Ability to apply appropriate styles
- Ability to create and insert graphs based on JSON-provided hierarchy

## Installation guide
1. Clone the repo to desired directory
1. Run `composer install`
1. ?

This will obviously be complemented once something is operational.

## Architecture

Originally based on [RPG Silex boilerplate](/mikron-ia/rpg-boilerplate-silex). Intended for non-interactive use.
