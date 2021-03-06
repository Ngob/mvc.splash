<?php
namespace Mouf\Mvc\Splash;

use Mouf\Composer\ClassNameMapper;
use Mouf\Mvc\Splash\Utils\SplashException;

/**
 * This class is in charge of all tasks that generate files:
 *  - .htaccess generation
 *  - controllers generation
 *  ...
 *  
 * @author David Négrier <david@mouf-php.com>
 */
class SplashGenerateService {
	/**
	 * Writes the .htaccess file
	 * 
	 * @param array<string> $exludeExtentions
	 * @param array<string> $exludeFolders
	 */
	public function writeHtAccess($exludeExtentions, $exludeFolders) {

		$modelsDirName = dirname(__FILE__);
		$splashDir = dirname($modelsDirName);
		$splashVersion = basename($splashDir);
		
		$strExtentions = implode('|', $exludeExtentions);
		$strFolders = '^' . implode('|^', $exludeFolders);
		
		$str = "<IfModule !mod_rewrite.c>
	# Use an error page as index file. It makes sure a proper error is displayed if
	# mod_rewrite is not available. Additionally, this reduces the matching process for the
	# start page (path \"/\") because otherwise Apache will apply the rewriting rules
	# to each configured DirectoryIndex file (e.g. index.php, index.html, index.pl).
	DirectoryIndex vendor/mouf/mvc.splash/src/rewrite_missing.php
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On

	# .htaccess RewriteBase related tips courtesy of Symfony 2's skeleton app.

    # Determine the RewriteBase automatically and set it as environment variable.
    # If you are using Apache aliases to do mass virtual hosting or installed the
    # project in a subdirectory, the base path will be prepended to allow proper
    # resolution of the base directory and to redirect to the correct URI. It will
    # work in environments without path prefix as well, providing a safe, one-size
    # fits all solution. But as you do not need it in this case, you can comment
    # the following 2 lines to eliminate the overhead.
    RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\\2$
    RewriteRule ^(.*) - [E=BASE:%1]

    # If the requested filename exists, and has an allowed extension, simply serve it.
    # We only want to let Apache serve files and not directories.
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule .*((\\.($strExtentions)$)|$strFolders) - [L]

    # Rewrite all other queries to the front controller.
    RewriteRule .? %{ENV:BASE}/vendor/mouf/mvc.splash/src/splash.php [L]
</IfModule>";
		
		file_put_contents(ROOT_PATH."../../../.htaccess", $str);
		chmod(ROOT_PATH."../../../.htaccess", 0664);
	}

}