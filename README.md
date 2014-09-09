PiklistHelper
=============

A helper for adding additional filters, validations, and functionality to [Piklist](piklist.com). Piklist is a framework built on top of Wordpress. It takes a lot of common tasks (such as creating meta-boxes, settings pages, etc.) and makes it fast and easy. On top of that it makes things like Post to Post relationships and taxonomy meta possible.

### Why a Piklist Helper? Isn't Piklist Perfect?

Nearly, yes. But Piklist attempts to be as light-weight as possible, and consequently will often provide the means to accomplish something, but not necessarily start performing the specific functionality thereof. Validations, for example, are an easy way to check user input in a meta-box when submitted. Piklist provides a small list of out-of-the-box validations, but there are many that come along regularly and would be great to not rewrite. That category of more opinionated functionality fits into PiklistHelper.

### What Should I submit to PiklistHelper?

Anything that you feel could improve the general experience with Piklist while remaining optional should certainly be submitted as an idea or pull-request. The only "rule" for this is that no functionality within PiklistHelper should break or force a change of the general Piklist practice. Validations, for example, may be used but there's no consequence (performance or otherwise) if they're not.

### How do I use it?

It's very easy! PiklistHelper is a general class with [static functions](http://php.net/manual/en/language.oop5.static.php). So just use them as you normally would. Everything in the PiklistHelper.php file is well documented, so feel free to read through it, and [phpdoc](http://www.phpdoc.org/) can be used to generate a document if you'd like.

To get started (including the validations), simply add the PiklistHelper.php file somewhere in your code and add the following to your functions.php or plugin root:
```
if ( !class_exists('PiklistHelper') ) {
  require_once('PiklistHelper.php');
  PiklistHelper::Initiate();
}
```

Happy coding!
