PiklistHelper
=============

### Before Upgrading to 0.6!

Please note that, as of 0.6, Piklist Helper is no longer just a class. It's now a plugin. This gives all the conveniences of plugins, allows things to be a bit more organized without complicating use, makes it usable in the theme and plugins without in-fighting, and removes the need to initialize it. Install, activate, and it just works. :)

---

A helper for adding additional filters, validations, and functionality to [Piklist](http://piklist.com). Piklist is a framework built on top of Wordpress. It takes a lot of common tasks (such as creating meta-boxes, settings pages, etc.) and makes it fast and easy. On top of that it makes things like Post to Post relationships and taxonomy meta possible.

### Why a Piklist Helper? Isn't Piklist Perfect?

Nearly, yes. But Piklist attempts to be as light-weight as possible, and consequently will often provide the means to accomplish something, but not necessarily start performing the specific functionality thereof. Validations, for example, are an easy way to check user input in a meta-box when submitted. Piklist provides a small list of out-of-the-box validations, but there are many that come along regularly and would be great to not rewrite. That category of more opinionated functionality fits into PiklistHelper.

### What Should I submit to PiklistHelper?

Anything that you feel could improve the general experience with Piklist while remaining optional should certainly be submitted as an idea or pull-request. The only "rule" for this is that no functionality within PiklistHelper should break or force a change of the general Piklist practice. Validations, for example, may be used but there's no consequence (performance or otherwise) if they're not.

### How do I use it?

Just install it like you would any other plugin! There are also functions available for use if you'd like. Just check out /includes/class-piklist-helper.php!

Happy coding!
