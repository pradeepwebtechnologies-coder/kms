/**
 * Krishna Music School theme: navigation and the mobile call-to-action bar.
 * Progressive enhancement only; the site works without JavaScript.
 */
( function () {
	'use strict';

	var header = document.querySelector( '[data-km-header]' );
	var toggle = document.querySelector( '[data-km-nav-toggle]' );
	var nav = document.querySelector( '[data-km-nav]' );

	/* ---------- Mobile menu ---------- */

	function setOpen( open ) {
		if ( ! header || ! toggle ) {
			return;
		}
		header.classList.toggle( 'is-open', open );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		document.documentElement.classList.toggle( 'km-nav-open', open );
	}

	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function () {
			setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
		} );
		document.addEventListener( 'keydown', function ( ev ) {
			if ( ev.key === 'Escape' && header.classList.contains( 'is-open' ) ) {
				setOpen( false );
				toggle.focus();
			}
		} );
	}

	/* ---------- Sub-menus: a real button next to each parent link ---------- */

	if ( nav ) {
		nav.querySelectorAll( '.menu-item-has-children' ).forEach( function ( item, i ) {
			var link = item.querySelector( ':scope > a' );
			var sub = item.querySelector( ':scope > .sub-menu' );
			if ( ! link || ! sub ) {
				return;
			}
			sub.id = sub.id || 'km-sub-' + i;
			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'km-sub-toggle';
			btn.setAttribute( 'aria-expanded', 'false' );
			btn.setAttribute( 'aria-controls', sub.id );
			btn.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6"/></svg>';
			var label = document.createElement( 'span' );
			label.className = 'screen-reader-text';
			label.textContent = link.textContent.trim();
			btn.prepend( label );
			link.after( btn );
			btn.addEventListener( 'click', function () {
				var open = btn.getAttribute( 'aria-expanded' ) !== 'true';
				nav.querySelectorAll( '.km-sub-toggle[aria-expanded="true"]' ).forEach( function ( other ) {
					if ( other !== btn ) {
						other.setAttribute( 'aria-expanded', 'false' );
						other.parentElement.classList.remove( 'is-open' );
					}
				} );
				btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
				item.classList.toggle( 'is-open', open );
			} );
		} );

		document.addEventListener( 'click', function ( ev ) {
			if ( ! nav.contains( ev.target ) ) {
				nav.querySelectorAll( '.menu-item-has-children.is-open' ).forEach( function ( item ) {
					item.classList.remove( 'is-open' );
					var b = item.querySelector( '.km-sub-toggle' );
					if ( b ) {
						b.setAttribute( 'aria-expanded', 'false' );
					}
				} );
			}
		} );
	}

	/* ---------- Header shadow + mobile CTA bar ---------- */

	var sticky = document.querySelector( '[data-km-sticky]' );
	var formInView = false;

	if ( sticky && 'IntersectionObserver' in window ) {
		var forms = document.querySelectorAll( '.km-enquiry, .km-footer' );
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( e ) {
				e.target.dataset.kmVisible = e.isIntersecting ? '1' : '';
			} );
			formInView = Array.prototype.some.call( forms, function ( f ) {
				return f.dataset.kmVisible === '1';
			} );
			onScroll();
		} );
		forms.forEach( function ( f ) {
			io.observe( f );
		} );
	}

	var ticking = false;
	function onScroll() {
		var y = window.scrollY || window.pageYOffset;
		if ( header ) {
			header.classList.toggle( 'is-scrolled', y > 8 );
		}
		if ( sticky ) {
			var show = y > 480 && ! formInView;
			sticky.classList.toggle( 'is-visible', show );
			document.body.classList.toggle( 'km-sticky-on', show );
		}
		ticking = false;
	}
	window.addEventListener(
		'scroll',
		function () {
			if ( ! ticking ) {
				window.requestAnimationFrame( onScroll );
				ticking = true;
			}
		},
		{ passive: true }
	);
	onScroll();
} )();
