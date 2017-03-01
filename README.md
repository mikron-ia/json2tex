# JSON to DOC proof of concept
[![Build Status](https://travis-ci.org/mikron-ia/json2doc-poc.svg?branch=master)](https://travis-ci.org/mikron-ia/json2doc-poc)
[![Code Climate](https://codeclimate.com/github/mikron-ia/json2doc-poc/badges/gpa.svg)](https://codeclimate.com/github/mikron-ia/json2doc-poc)
[![Test Coverage](https://codeclimate.com/github/mikron-ia/json2doc-poc/badges/coverage.svg)](https://codeclimate.com/github/mikron-ia/json2doc-poc/coverage)

## Background
Some resources - in this case, game data - are better stored in simple format of JSON or XML and subsequently processed into more complex formats, like DB records or DOC file containing a game manual. This project is intended to present the latter case - JSON data conversion to DOC.

Desired functionalities:

- Ability to write down a JSON-described text with respect to hierarchy
- Ability to apply appropriate styles 
- Ability to create and insert SVG files based on JSON-provided hierarchy

## Installation guide
1. Clone the repo to desired directory
1. Run `composer install`
1. ?
1. PROFIT!

## Architecture

Originally based on [RPG Silex boilerplate](/mikron-ia/rpg-boilerplate-silex). Intended for non-interactive use.
