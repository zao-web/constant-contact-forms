<?php
/**
 * PHP-Scoper config.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  NEXT
 */

declare( strict_types=1 );

use Isolated\Symfony\Component\Finder\Finder;

return [

	/*
	 * By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
	 * directory. You can however define which files should be scoped by defining a collection of Finders in the
	 * following configuration key.
	 *
	 * For more see: https://github.com/humbug/php-scoper#finders-and-paths
	 */
	'prefix'                     => 'ccwp',
	'finders'                    => [
		Finder::create()->files()->in( 'vendor' )->name( [ '*.php', 'LICENSE', 'composer.json' ] ),
		Finder::create()->append([
			'composer.json',
		])
	],

];
