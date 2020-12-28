<?php
include_once 'simple_html_dom.php';
/**
* Oploverz v1.0
*/
class Anoboy
{
private $uri = "https://anoboy.tube";

public function newRelease(int $page = 1)	
{
	$output = array();
	$html = file_get_html("{$this->uri}/page/{$page}");
	if ($html == '') {
		return "Something was wrong";
	}
	$html = $html->find('div.home_index', 0);
	$c = substr_count($html, '<div class="amv">');
	$i = 0;
	while ($i < $c) {
		$temp = $html->find('div.amv', $i)->parent();
		$data['link'] = $this->getBetween($temp, 'a href="https://anoboy.tube/','/"');
		$data['title'] = $this->getBetween($temp, 'title="', '"');
		$data['image'] = str_replace('s240', 's480', $this->getBetween($temp, '<amp-img src="', '"'));
		$data['created_at'] = $this->getBetween($temp, '<div class="jamup">UP ','Wib</div>');
		array_push($output, $data);

		$i++;
	}
	return $output;

}
public function search(string $query = null, int $page = 1)
{
	$output = array();
	$html = file_get_html("{$this->uri}/page/{$page}/?s={$query}");
	if ($html == '') {
		return "Something was wrong";
	}
	$html = $html->find('div.column-content', 0);
	$c = substr_count($html, '<div class="amv">');
	$i = 0;
	while ($i < $c) {
		$temp = $html->find('div.amv', $i)->parent();
		$data['link'] = $this->getBetween($temp, 'a href="https://anoboy.tube/','/"');
		$data['title'] = $this->getBetween($temp, 'title="', '"');
		$data['image'] = str_replace('s240', 's480', $this->getBetween($temp, '<amp-img src="', '"'));
		$data['created_at'] = $this->getBetween($temp, '<div class="jamup">UP ','Wib</div>');
		array_push($output, $data);

		$i++;
	}
	return $output;

}

public function details($link)
{
	
	$html = file_get_html("{$this->uri}/{$link}");
	// Type 
	$type = $html->find('video', 0);
	if ($type) {
		return $this->_tv($html);
	}else {
		return $this->_batch($html);
	}
	
}

private function _batch($html)
{
	// Details

	$tempDetails = ($html->find('div.contenttable', 0)) ? $html->find('div.contenttable', 0) : null;
	$thumb = ($html->find('div.entry-content', 0)->find('amp-img', 0)->src) ? str_replace('s240', 's720', $html->find('div.entry-content', 0)->find('amp-img', 0)->src) : null;
	$title = ($this->getBetween($html, '<h2 class="entry-title" itemprop="name">',"</h2>")) ? $this->getBetween($html, '<h2 class="entry-title" itemprop="name">',"</h2>") : null;
	$studio = ($tempDetails) ? $tempDetails->find('tr', 1)->find('td', 0)->innertext : null;
	$duration = ($tempDetails) ? $tempDetails->find('tr', 3)->find('td', 0)->innertext : null;
	$genre = ($tempDetails) ? $tempDetails->find('tr', 4)->find('td', 0)->innertext : null;
	$genre = ($genre == null) ? null : explode(", ", $genre);
	$rating = ($tempDetails) ? $tempDetails->find('tr', 5)->find('td', 0)->innertext : null;
	$desc = ($html->find('div.contentdeks', 0)->innertext) ? $html->find('div.contentdeks', 0)->innertext : null;
	$eps = ($tempDetails) ? $tempDetails->find('tr', 0)->find('td a', 0)->href : null;


	$tempBatch = $html->find('div.unduhan', 1);
	
	$batch['360'] = $tempBatch->find('a', 1)->href;
	$batch['480'] = $tempBatch->find('a', 2)->href;
	$batch['720'] = $tempBatch->find('a', 3)->href;

	$zip = array();
	$temp360 = $html->find('table.tigaenampuluh', 0);
	$temp480 = $html->find('table.empatlapanpuluh', 0);
	$temp720 = $html->find('table.tujuduapuluh', 0);
	$c = substr_count($temp360, "<tr>");
	$i = 1;
	while ($i < $c) {
		$zippy['360'] = $temp360->find('tr', $i)->find('td a', 0)->href;
		$zippy['480'] = $temp480->find('tr', $i)->find('td a', 0)->href;
		$zippy['720'] = $temp720->find('tr', $i)->find('td a', 0)->href;

		array_push($zip, $zippy);
		$i++;
	}
	$output = array(
		"type" => 'batch',
		"details" => array(
			"title" => $title,
			"thumb" => $thumb,
			"studio" => $studio,
			"duration" => $duration,
			"genre" => $genre,
			"rating" => $rating,
			"description" => $desc,
			"more_episodes" => $eps
		),
		"download" => array(
			"batch" => $batch,
			"single" => $zip
		)
	);
	return $output;
}

private function _tv($html){
	// Details
	$tempDetails = ($html->find('div.contenttable', 0)) ? $html->find('div.contenttable', 0) : null;
	$thumb = ($html->find('div.entry-content', 0)->find('amp-img', 0)->src) ? str_replace('s240', 's720', $html->find('div.entry-content', 0)->find('amp-img', 0)->src) : null;
	$title = ($this->getBetween($html, '<h2 class="entry-title" itemprop="name">',"</h2>")) ? $this->getBetween($html, '<h2 class="entry-title" itemprop="name">',"</h2>") : null;
	$studio = ($tempDetails) ? $tempDetails->find('tr', 1)->find('td', 0)->innertext : null;
	$duration = ($tempDetails) ? $tempDetails->find('tr', 3)->find('td', 0)->innertext : null;
	$genre = ($tempDetails) ? $tempDetails->find('tr', 4)->find('td', 0)->innertext : null;
	$genre = ($genre == null) ? null : explode(", ", $genre);
	$rating = ($tempDetails) ? $tempDetails->find('tr', 5)->find('td', 0)->innertext : null;
	$desc = ($html->find('div.contentdeks', 0)->innertext) ? $html->find('div.contentdeks', 0)->innertext : null;
	$eps = ($tempDetails) ? $tempDetails->find('tr', 0)->find('td a', 0)->href : null;


	// Download
	$_360 = $html->find('video[preload=auto]',0);
	$_720 = $html->find('div.vmiror', 0)->find('a', 1);
	$tempZippy = $html->find('div.vmiror', 2);

	$gd['360'] = $this->getBetween($_360, 'src="', '" type');
	$gd['720'] = $this->uri . $this->getBetween($_720, 'data-video="','"');
	$gd['720'] = file_get_html($gd['720']);
	$gd['720'] = $gd['720']->find('source', 0)->src;
	$zip = $this->getBetween($tempZippy, 'zip.php?', '"');
	parse_str($zip, $zippy);

	// Output
	$output = array(
		"type" => 'tv',
		"details" => array(
			"title" => $title,
			"thumb" => $thumb,
			"studio" => $studio,
			"duration" => $duration,
			"genre" => $genre,
			"rating" => $rating,
			"description" => $desc,
			"more_episodes" => $eps
		),
		"download" => array(
			'360' => array(
				"zippyshare" => $zippy['data2'],
				"googlevideo" => $gd['360']
			),
			'480' => array(
				"zippyshare" => $zippy['data3'],
				"googlevideo" => null
			),
			'720' => array(
				"zippyshare" => $zippy['data4'],
				"googlevideo" => $gd['720']
			)
		)
			
	);
	return $output;	
}

private function getBetween($content, $start, $end)
	{
		$r = explode($start, $content);
		if (isset($r[1])){
			$r = explode($end, $r[1]);
			return $r[0];
		}
		return '';
	}
}

// $p = new Anoboy;
// // echo "<pre>";
// // // echo $p->NewRelease();
// header('Content-type: application/json');
// echo json_encode($p->details('2020/12/assault-lily-bouquet-download'));