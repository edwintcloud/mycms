/*
Spoilers is a jQuery plugin which implements a feature first seen
on the imdb.com web site. For an example, see:

http://imdb.com/title/tt0084787/faq

Scroll down that page until you see some red "Spoilers!" text, then
move your mouse over that text. Moving the mouse over it causes "Spoilers!"
to disappear and the underlying text to be revealed. The intention is to
keep the casual reader from inadvertently reading details which he might
not want to know. By mousing over them he can reveal the details.

This plugin implements that behaviour.

Usage:

$(selector).initSpoilers( { ... options ... } );

Where the options are all optional:

	- revealedClass [string, default='reveal'], a CSS class name
	applied to text which should be revealed. See the CSS examples
	for how this works.

	- method [string, default='hover'], one of ('hover','hoverIntent','click').
	If 'hover' is used then the spoiler text is revealed upon mouseEnter
	and hidden upon mouseOut. The 'hoverIntent' option is identical but uses
	the hoverIntent function, which is provided by an additional plugin.
	If 'click' is used then clicking the spoiler text toggles it on and off.

	- hoverIntent [Object, no default], is ONLY used if method:'hoverIntent'
	is set. This object should contain any options to pass to hoverIntent(),
	except for the 'over' and 'out' functions, which will be supplied by this
	plugin. e.g. hoverIntent:{interval:1000,timeout:1000}
	If you supply over/out properties they will be ignored/overridden.
	See the hoverIntent home page for full information about the options:
	http://cherne.net/brian/resources/jquery.hoverIntent.html

///////////////////////////////////////////////////////////////////////
Example:

The CSS:

The first two definitions apply to "hidden" text:
.jqSpoiler {
	background-image:url(spoilers.png);
	border:1px solid red;
}
.jqSpoiler span {
	visibility: hidden;
}

Note that the background-image graphic should (ideally) not have a
transparent background because the underlying text will show through.
Alternately, if you use a "noisy enough" graphic, a transparent
background can provide decent results.

The second two definitions apply to "revealed" text:

.jqSpoiler.reveal {
	background-image: none;
	border: none;
}
.jqSpoiler.reveal span {
	visibility: visible;
} 


The HTML:

<span class='jqSpoiler'><span>The hidden text goes here.</span></span>

Note that there is nothing special about the class jqSpoiler - you may
use any name you like. The 'reveal' class name can be changed by passing
{revealedClass:'MyClass'} to initSpoilers(). Note that it is not legal
to add block-level HTML elements inside of a SPAN element, so you cannot
spoiler a whole table or DIV this way. You can of course modify your CSS
code to work around this.

The JavaScript:

$('.jqSpoiler').initSpoilers();


///////////////////////////////////////////////////////////////////////
Potential TODOs:

- ???

- While it would be trivial to add onReveal/onUnreveal handlers, that
would probably be waaaayyy overkill for this plugin.

///////////////////////////////////////////////////////////////////////
Author:

	http://wanderinghorse.net/home/stephan/

With many thanks to Michael Geary and Jay Salvat for doing the
detective work in figuring out how this worked on the imdb.com site:

http://groups.google.com/group/jquery-en/browse_thread/thread/9d34cad45e541e36/8f02c8f79b5c7985

Plugin home page:

	http://wanderinghorse.net/computing/javascript/jquery/spoilers/

License: Public Domain

Revision history:

- 20070808: initial release

- 20070809:
	- replaced $() with jQuery().
	- Added method:'...' support for 'hover', 'click', 'hoverIntent'

*/
jQuery.fn.initSpoilers = function( props ) {
	props = jQuery.extend({
		revealedClass:'reveal',
		method:'hover'
		},
		props ? props : {});

	function over() {
		jQuery(this).addClass( props.revealedClass );
	};
	function out() {
		jQuery(this).removeClass( props.revealedClass );
	};

	if( 'hover' === props.method ) {
		this.hover( over, out );
	} else if( 'hoverIntent' === props.method ) {
		var hoverIntentOps = ('hoverIntent' in props) ? props.hoverIntent : {};
		hoverIntentOps.over = over;
		hoverIntentOps.out = out;
		this.hoverIntent( hoverIntentOps );
	} else if ( 'click' === props.method ) {
		this.toggle( over, out );
	}
	return this;
};
