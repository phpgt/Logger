<?php
namespace Gt\Logger\Test\Helper;

use php_user_filter;

class StdOutToEcho extends php_user_filter {
	public function filter($in, $out, &$consumed, $closing) {
		$buffer = "";
		while($bucket = stream_bucket_make_writeable($in)) {
			$buffer .= $bucket->data;
			$consumed += $bucket->datalen;
			stream_bucket_append($out, $bucket);
		}

		echo $buffer;

		return PSFS_FEED_ME;
	}
}