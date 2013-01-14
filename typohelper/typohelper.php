<?php
/*
Plugin Name: Typohelper
Description: Русская типографика. В основном расставляет неразрывные пробелы. Тире и кавычки уже должны стоять.
Author: Artem Sapegin
Author URI: http://sapegin.ru/
Plugin URI: http://sapegin.ru/wordpress
Version: 0.4
*/

/*
define('WP_TYPOHELPER_DUMMY', true);
*/

// отключаем встроенный типографер
remove_filter('category_description', 'wptexturize');
remove_filter('list_cats', 'wptexturize');
remove_filter('comment_author', 'wptexturize');
remove_filter('comment_text', 'wptexturize');
remove_filter('single_post_title', 'wptexturize');
remove_filter('the_title', 'wptexturize');
remove_filter('the_content', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('the_content_rss', 'wptexturize');

// переопределяем фильтры с приоритетом 10 (как и Texturize).
add_filter('category_description', 'typo_lite', 10);
add_filter('list_cats', 'typo_lite', 10);
add_filter('comment_author', 'typo_lite', 10);
add_filter('comment_text', 'typo_lite', 10);
add_filter('single_post_title', 'typo_process', 10);
add_filter('the_title', 'typo_process', 10);
add_filter('the_content', 'typo_process', 10);
add_filter('the_excerpt', 'typo_process', 10);
add_filter( 'the_content_rss', 'typo_process' );

$typo_tags = array ();

function typo_backup_tags($s)
{
	global $typo_tags;
	$typo_tags[] = $s[1];
	return "<≈>";
}

function typo_restore_tags($s)
{
	global $typo_tags;
	return array_shift($typo_tags);
}

/*
 * Обогащение типографики. При условии, что кавычки, тире и прочие знаки уже стоят.
 */
function typo_process($s)
{
	// убиваем табуляцию
	$s = str_replace("\t", '', $s);
	
 	// убиваем повторяющиеся пробелы
	$s = preg_replace('% +%', ' ', $s);

	if (defined('WP_TYPOHELPER_DUMMY') && WP_TYPOHELPER_DUMMY) {
		$s = typo_process_lite($s);
	}
	
 	// исправляем неразрывные пробелы
	$s = str_replace("\xA9", '&nbsp;', $s);
	
	// сохраняем теги
	$s = preg_replace_callback('%(<[^>]*>)%ums', 'typo_backup_tags', $s);

	$search  = array (
		'№ ',			// номер
		'§ ',			// параграф
		' —',			// тире
		'и т. д.',
		'и т. п.',
	);
	$replace = array (
		'№&nbsp;',
		'§&nbsp;',
		'&nbsp;—',
		'и&nbsp;т.&nbsp;д.',
		'и&nbsp;т.&nbsp;п.',
	);
	$s = str_replace( $search, $replace, $s );

	// год
	$s = preg_replace( '%(?<![0-9])([0-9]{4}) (г\.)%ui', '\\1&nbsp;\\2', $s );
	
	// имена собственные
	// $s = preg_replace( '%(?<![а-яёА-ЯЁ])([гГ]|[гГ]р|[тТ]ов)\. ([А-ЯЁ])%u', '\\1.&nbsp;\\2', $s );
	
	// инициалы
	$s = preg_replace( '%(?<![а-яёА-ЯЁ])((?:[А-ЯЁ]\. ){1,2}[А-ЯЁ][-а-яё]+)%u', '<span class="nobr">\\1</span>', $s );
	
	// слова через дефис
	$s = preg_replace( '%(?<![а-яё])((?:[а-яё]{1,2}(?:\-[а-яё]+))|(?:[а-яё]+(?:\-[а-яё]{1,2})))(?![а-яё])%ui', '<span class="nobr">\\1</span>', $s );
	
	// частицы
	$s = preg_replace( '% (ж|бы|б|же|ли|ль|либо|или)(?![а-яё])%ui', '&nbsp;\\1', $s );
	
	// предлоги и союзы
	$s = preg_replace( '%(?<![а-яё])(а|в|во|вне|и|или|к|о|с|у|о|со|об|обо|от|ото|то|на|не|ни|но|из|изо|за|уж|на|по|под|подо|пред|предо|про|над|надо|как|без|безо|что|да|для|до|там|ещё|их|или|ко|меж|между|перед|передо|около|через|сквозь|для|при|я)\s%ui', '\\1&nbsp;', $s );

	// валюты
	$s = preg_replace( '%(\d) (\$|р\.|руб\.)%ui', '\\1&nbsp;\\2', $s );
	
	// даты
	$s = preg_replace( '%(\d) (января|февраля|марта|апреля|мая|июня|июля|августа|сентября|ноября|декабря)%ui', '\\1&nbsp;\\2', $s );
	
	// восстанавливаем теги
	$s = preg_replace_callback("/<≈>/u", 'typo_restore_tags', $s);

	return trim( $s );
}

/*
 * Типографика в стиле Word'а. Для комментариев и прочего неконтролируемого текста.
 */
function typo_lite( $s )
{
	// убиваем табуляцию
	$s = str_replace("\t", '', $s);
	
 	// убиваем повторяющиеся пробелы
	$s = preg_replace('% +%', ' ', $s);
	
 	// сохраняем теги
	$s = preg_replace_callback('%(<[^>]*>)%ums', 'typo_backup_tags', $s);

	$s = typo_process_lite($s);
	
	// исправляем неразрывные пробелы
	$s = str_replace("\xA9", '&nbsp;', $s);	

	// восстанавливаем теги
	$s = preg_replace_callback("/<≈>/u", 'typo_restore_tags', $s);
	
	return $s;
}

function typo_process_lite($s) {
	// кавычки
	$s = preg_replace( '%"([а-яёa-z<])%ui', '«\\1', $s );
	$s = preg_replace( '%([а-яёa-z>])"%ui', '\\1»', $s );
	
	// тире
	$s = str_replace('--', '—', $s);
	$s = preg_replace('%(^|[> \xA0])-|—($| )%u', '\\1—\\2', $s);
	
	// апостроф
	$s = str_replace("'", '’', $s);

	// многоточие
	$s = str_replace("...", '…', $s);

	// копирайт
	$s = str_replace("(C)", '©', $s);	
	$s = str_replace("(c)", '©', $s);
	
	return $s;
}

?>