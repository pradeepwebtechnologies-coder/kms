/**
 * KMS Core widgets: currency switcher, time-zone converter, enquiry form.
 * No dependencies. Everything degrades to the server-rendered HTML without JavaScript.
 */
( function () {
	'use strict';

	var cfg = window.kmsCore || {};
	var i18n = cfg.i18n || {};
	var STORE_KEY = 'kmsCurrency';
	var SYMBOLS = { USD: 'US$', EUR: '€', GBP: '£', INR: '₹' };

	function track( name, params ) {
		try {
			if ( typeof window.gtag === 'function' ) {
				window.gtag( 'event', name, params || {} );
			}
			if ( Array.isArray( window.dataLayer ) ) {
				window.dataLayer.push( Object.assign( { event: name }, params || {} ) );
			}
		} catch ( e ) {}
	}

	function visitorZone() {
		try {
			return Intl.DateTimeFormat().resolvedOptions().timeZone || '';
		} catch ( e ) {
			return '';
		}
	}

	/* ---------- Currency ---------- */

	function guessCurrency() {
		var tz = visitorZone();
		if ( /^Asia\/(Kolkata|Calcutta)$/.test( tz ) ) {
			return 'INR';
		}
		if ( tz === 'Europe/London' || tz === 'Europe/Belfast' ) {
			return 'GBP';
		}
		if ( /^Europe\//.test( tz ) ) {
			return 'EUR';
		}
		return 'USD';
	}

	function savedCurrency() {
		try {
			return window.localStorage.getItem( STORE_KEY );
		} catch ( e ) {
			return null;
		}
	}

	function applyCurrency( cur ) {
		document.querySelectorAll( '.km-price' ).forEach( function ( el ) {
			var alt = el.querySelector( '.km-price__alt' );
			if ( ! alt ) {
				return;
			}
			if ( cur === 'INR' ) {
				alt.hidden = true;
				return;
			}
			var value = el.getAttribute( 'data-' + cur.toLowerCase() );
			if ( ! value || value === '0' ) {
				alt.hidden = true;
				return;
			}
			var exact = el.getAttribute( 'data-exact' ) === cur.toLowerCase();
			alt.hidden = false;
			alt.textContent = ( exact ? '' : '≈ ' ) + SYMBOLS[ cur ] + Number( value ).toLocaleString( 'en-US', { maximumFractionDigits: 0 } );
		} );
		document.querySelectorAll( '.km-currency__btn' ).forEach( function ( btn ) {
			btn.setAttribute( 'aria-pressed', btn.getAttribute( 'data-cur' ) === cur ? 'true' : 'false' );
		} );
	}

	function initCurrency() {
		if ( ! document.querySelector( '.km-price' ) ) {
			return;
		}
		applyCurrency( savedCurrency() || guessCurrency() );
		document.addEventListener( 'click', function ( ev ) {
			var btn = ev.target.closest( '.km-currency__btn' );
			if ( ! btn ) {
				return;
			}
			var cur = btn.getAttribute( 'data-cur' );
			applyCurrency( cur );
			try {
				window.localStorage.setItem( STORE_KEY, cur );
			} catch ( e ) {}
		} );
	}

	/* ---------- Time zones ---------- */

	function istToDate( hm ) {
		var parts = String( hm ).split( ':' );
		var now = new Date();
		// IST is UTC+5:30 all year (no daylight saving).
		return new Date( Date.UTC( now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(), Number( parts[ 0 ] ) - 5, Number( parts[ 1 ] || 0 ) - 30 ) );
	}

	function initTimezones() {
		var zone = visitorZone();
		if ( ! zone || /^Asia\/(Kolkata|Calcutta)$/.test( zone ) || ! window.Intl ) {
			return;
		}
		var timeFmt, dayFmt, zoneName = '';
		try {
			timeFmt = new Intl.DateTimeFormat( undefined, { hour: 'numeric', minute: '2-digit' } );
			dayFmt = new Intl.DateTimeFormat( 'en-CA', { year: 'numeric', month: '2-digit', day: '2-digit' } );
			var named = new Intl.DateTimeFormat( undefined, { timeZoneName: 'short' } ).formatToParts( new Date() );
			named.forEach( function ( p ) {
				if ( p.type === 'timeZoneName' ) {
					zoneName = p.value;
				}
			} );
		} catch ( e ) {
			return;
		}
		document.querySelectorAll( '[data-km-tz]' ).forEach( function ( el ) {
			var start = istToDate( el.getAttribute( 'data-start' ) || '09:00' );
			var end = istToDate( el.getAttribute( 'data-end' ) || '21:00' );
			var text = timeFmt.format( start ) + ' – ' + timeFmt.format( end );
			if ( dayFmt.format( start ) !== dayFmt.format( end ) ) {
				text += ' (' + ( i18n.nextDay || 'next day' ) + ')';
			}
			text += ' ' + ( i18n.yourTime || 'your time' ) + ( zoneName ? ' (' + zoneName + ')' : '' );
			var out = el.querySelector( '[data-km-tz-local]' );
			var wrap = el.querySelector( '.km-tz__local' );
			if ( out && wrap ) {
				out.textContent = text;
				wrap.hidden = false;
			}
		} );
	}

	/* ---------- Enquiry form ---------- */

	function initForms() {
		var params = new URLSearchParams( window.location.search );
		var course = params.get( 'course' );

		document.querySelectorAll( '[data-km-form]' ).forEach( function ( form ) {
			var set = function ( sel, value ) {
				var input = form.querySelector( sel );
				if ( input ) {
					input.value = value;
				}
			};
			set( '[data-km-js]', '1' );
			set( '[data-km-ts]', String( Date.now() ) );
			set( '[data-km-tzfield]', visitorZone() );
			set( '[data-km-page]', window.location.href.split( '#' )[ 0 ] );

			var interest = form.querySelector( '[data-km-interest]' );
			if ( interest && course && interest.querySelector( 'option[value="' + course.replace( /[^a-z0-9-]/gi, '' ) + '"]' ) ) {
				interest.value = course;
			}

			var wrap = form.closest( '.km-enquiry' );
			var done = wrap ? wrap.querySelector( '[data-km-done]' ) : null;
			var error = form.querySelector( '[data-km-error]' );
			var button = form.querySelector( '[data-km-submit]' );

			form.addEventListener( 'submit', function ( ev ) {
				if ( ! window.fetch || ! window.FormData ) {
					return; // Normal POST + redirect.
				}
				ev.preventDefault();
				var label = button ? button.textContent : '';
				if ( button ) {
					button.disabled = true;
					button.textContent = i18n.sending || 'Sending…';
				}
				if ( error ) {
					error.hidden = true;
				}
				// getAttribute: a field named "action" shadows form.action.
				fetch( form.getAttribute( 'action' ), {
					method: 'POST',
					body: new FormData( form ),
					headers: { 'X-KMS-AJAX': '1' },
					credentials: 'same-origin',
				} )
					.then( function ( res ) {
						return res.json().catch( function () {
							return { success: false };
						} );
					} )
					.then( function ( json ) {
						if ( ! json || ! json.success ) {
							throw new Error( 'failed' );
						}
						track( 'generate_lead', { form: 'kms_enquiry', interest: interest ? interest.value : '' } );
						form.hidden = true;
						if ( done ) {
							done.hidden = false;
							done.focus();
						}
					} )
					.catch( function () {
						if ( button ) {
							button.disabled = false;
							button.textContent = label;
						}
						if ( error ) {
							error.textContent = i18n.error || 'Sorry, that did not go through.';
							error.hidden = false;
						}
					} );
			} );
		} );
	}

	/* ---------- WhatsApp click tracking ---------- */

	function initTracking() {
		document.addEventListener( 'click', function ( ev ) {
			var link = ev.target.closest( 'a[href*="wa.me/"]' );
			if ( link ) {
				track( 'whatsapp_click', { link_url: link.href } );
			}
		} );
	}

	function init() {
		initCurrency();
		initTimezones();
		initForms();
		initTracking();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
