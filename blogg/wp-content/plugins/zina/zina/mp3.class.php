<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * ZINA (Zina is not Andromeda)
 *
 * Zina is a graphical interface to your MP3 collection, a personal
 * jukebox, an MP3 streamer. It can run on its own, embeded into an
 * existing website, or as a Drupal/Joomla/Wordpress/etc. module.
 *
 * http://www.pancake.org/zina
 * Author: Ryan Lathouwers <ryanlath@pacbell.net>
 * Support: http://sourceforge.net/projects/zina/
 * License: GNU GPL2 <http://www.gnu.org/copyleft/gpl.html>
 *
 * MP3/OGG file info and tags
 *
 * hacked pretty heavily from...
 * MP3::Info by Chris Nandor <http://sf.net/projects/mp3-info/>
 * class.id3.php by Sandy McArthur, Jr. <http://Leknor.com/code/>
 * getID3() [ogg stuff] by James Heinrich <http://www.silisoftware.com>
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
#TODO: propbably should be renamed
class mp3 {
	function mp3($file, $info=false, $tag=false, $faster=true, $genre = false) {
		$this->file = $file;
		$this->tag = 0;
		$this->info = 0;
		$this->faster = $faster;
		#TODO: get fh once???

		if (stristr(substr($file,-3),'mp3')) {
 			if ($info) $this->getMP3Info();
			if ($tag) $this->getID3Tag($genre);
		} elseif (stristr(substr($file,-3),'ogg')) {
			if ($info || $tag) $this->getOgg($info, $tag);
		} else {
			$this->getBadInfo();
		}
	}
	function getID3Tag($genre) {
		$this->tag = 0;
		$v2h = null;

		if (!($fh = @fopen($this->file, 'rb'))) { return 0; }

		$v2h = $this->getV2Header($fh);

		if (!empty($v2h) && !($v2h->major_ver < 2)) {
			$hlen = 10; $num = 4;

			if ($v2h->major_ver == 2) { $hlen = 6; $num = 3; }

			$off = 10; #TODO ext_header?
			$size = null;
			#ALBUM: 2 TAL, 3 TALB
			$map = array(
				'2'=>array('TT2'=>'title', 'TAL'=>'album', 'TP1'=>'artist', 'TYE'=>'year', 'TCO'=>'genre', 'TRK'=>'track'),
				'3'=>array('TIT2'=>'title', 'TALB'=>'album', 'TPE1'=>'artist', 'TYER'=>'year', 'TCON'=>'genre', 'TRCK'=>'track')
				);
			$fs = sizeof($map[2]);
			$this->title = $this->artist = null;

			while($off < $v2h->tag_size) {
				$arr = $id = null;
				$found = 0;
				fseek($fh, $off);
				$bytes = fread($fh, $hlen);
				if (preg_match("/^([A-Z0-9]{".$num."})/", $bytes, $arr)) {
					$id = $arr[0];
					$size = $hlen;
					$bytes = array_reverse(unpack("C$num",substr($bytes,$num,$num)));
					for ($i=0; $i<($num - 1); $i++) {
						$size += $bytes[$i] * pow(256,$i);
					}
				} else { break; }
				fseek($fh, $off + $hlen);
				if ($size > $hlen) {
					$bytes = fread($fh, $size - $hlen);
					if (isset($map[$v2h->major_ver][$id])) {
						if (ord($bytes[0]) == 1) {
							#STRIP ENCODING...???
							$this->$map[$v2h->major_ver][$id] = str_replace("\0",'',substr(trim($bytes),3));
							#$this->$map[$v2h->major_ver][$id] = mb_convert_encoding($bytes,mb_internal_encoding(),mb_detect_encoding($bytes));
						} else {
							$this->$map[$v2h->major_ver][$id] = trim($bytes);
						}
						#$this->$map[$v2h->major_ver][$id] .= '::'.ord($bytes[0]);
						$this->tag = 1;
						$this->tag_version = "ID3v2.".$v2h->major_ver;
						if (++$found == $fs) { break; }
					}
				}
				$off += $size;
			}
		}
		#if v2 not found look for v1
		if (!$this->tag) {
			if (fseek($fh, -128, SEEK_END) == -1) { return 0; }
			$tag = fread($fh, 128);

			if (substr($tag,0,3) == "TAG") {
				if ($tag[125] == Chr(0) and $tag[126] != Chr(0)) {
					$format = 'a3TAG/a30title/a30artist/a30album/a4year/a28comment/x1/C1track/C1genre';
					$this->tag_version = "ID3v1.1";
				} else {
					$format = 'a3TAG/a30title/a30artist/a30album/a4year/a30comment/C1genre';
					$this->tag_version = "ID3v1";
				}
				$id3tag = unpack($format, $tag);
				foreach ($id3tag as $key=>$value) {
					$this->$key = trim($value);
				}
				unset($this->TAG);
				$this->tag = 1;
			}
		}
		fclose($fh);

		if ($genre) {
			$genres = array(
0=>'Blues',1=>'Classic Rock',2=>'Country',3=>'Dance',4=>'Disco',
5=>'Funk',6=>'Grunge',7=>'Hip-Hop',8=>'Jazz',9=>'Metal',10=>'New Age',
11=>'Oldies',12=>'Other',13=>'Pop',14=>'R&B',15=>'Rap',16=>'Reggae',
17=>'Rock',18=>'Techno',19=>'Industrial',20=>'Alternative',21=>'Ska',
22=>'Death Metal',23=>'Pranks',24=>'Soundtrack',25=>'Euro-Techno',
26=>'Ambient',27=>'Trip-Hop',28=>'Vocal',29=>'Jazz+Funk',30=>'Fusion',
31=>'Trance',32=>'Classical',33=>'Instrumental',34=>'Acid',35=>'House',
36=>'Game',37=>'Sound Clip',38=>'Gospel',39=>'Noise',40=>'Alternative Rock',
41=>'Bass',42=>'Soul',43=>'Punk',44=>'Space',45=>'Meditative',46=>'Instrumental Pop',
47=>'Instrumental Rock',48=>'Ethnic',49=>'Gothic',50=>'Darkwave',
51=>'Techno-Industrial',52=>'Electronic',53=>'Pop-Folk',54=>'Eurodance',
55=>'Dream',56=>'Southern Rock',57=>'Comedy',58=>'Cult',59=>'Gangsta',
60=>'Top 40',61=>'Christian Rap',62=>'Pop/Funk',63=>'Jungle',64=>'Native US',
65=>'Cabaret',66=>'New Wave',67=>'Psychadelic',68=>'Rave',69=>'Showtunes',
70=>'Trailer',71=>'Lo-Fi',72=>'Tribal',73=>'Acid Punk',74=>'Acid Jazz',
75=>'Polka',76=>'Retro',77=>'Musical',78=>'Rock & Roll',79=>'Hard Rock',
80=>'Folk',81=>'Folk-Rock',82=>'National Folk',83=>'Swing',84=>'Fast Fusion',
85=>'Bebob',86=>'Latin',87=>'Revival',88=>'Celtic',89=>'Bluegrass',90=>'Avantgarde',
91=>'Gothic Rock',92=>'Progressive Rock',93=>'Psychedelic Rock',94=>'Symphonic Rock',
95=>'Slow Rock',96=>'Big Band',97=>'Chorus',98=>'Easy Listening',99=>'Acoustic',
100=>'Humour',101=>'Speech',102=>'Chanson',103=>'Opera',104=>'Chamber Music',
105=>'Sonata',106=>'Symphony',107=>'Booty Bass',108=>'Primus',109=>'Porn Groove',
110=>'Satire',111=>'Slow Jam',112=>'Club',113=>'Tango',114=>'Samba',115=>'Folklore',
116=>'Ballad',117=>'Power Ballad',118=>'Rhytmic Soul',119=>'Freestyle',120=>'Duet',
121=>'Punk Rock',122=>'Drum Solo',123=>'Acapella',124=>'Euro-House',125=>'Dance Hall',
126=>'Goa',127=>'Drum & Bass',128=>'Club-House',129=>'Hardcore',130=>'Terror',
131=>'Indie',132=>'BritPop',133=>'Negerpunk',134=>'Polsk Punk',135=>'Beat',
136=>'Christian Gangsta Rap',137=>'Heavy Metal',138=>'Black Metal',139=>'Crossover',
140=>'Contemporary Christian',141=>'Christian Rock',142=>'Merengue',143=>'Salsa',
144=>'Trash Metal',145=>'Anime',146=>'Jpop',147=>'Synthpop',255=>'Unknown'
);

			if ($this->tag && !empty($this->genre)) {
				$this->genre = (preg_match("/\((.*?)\)/",$this->genre, $match)) ? $match[1] : ucfirst(trim($this->genre));
				if (is_numeric($this->genre)) {
					$this->genre = (isset($genres[$this->genre])) ? $genres[$this->genre] : 'Unknown';
				}
			} else {
				$this->genre = 'Unknown';
			}
		}
		return $this->tag;
	}

	function getMP3Info() {
		$file = $this->file;

		if (! ($f = fopen($file, 'rb')) ) { return false; }

		$this->filesize = filesize($file);
		$frameoffset = 0;
		$total = 4096;

		if ($frameoffset == 0) {
			if ($v2h = $this->getV2Header($f)) {
				$total += $frameoffset += $v2h->tag_size;
				fseek($f, $frameoffset);
			} else {
				fseek($f, 0);
			}
		}

		if ($this->faster) {
			do {
				while (fread($f,1) != Chr(255)) { // Find the first frame
					if (feof($f)) { return false; }
				}
				fseek($f, ftell($f) - 1); // back up one byte
				$frameoffset = ftell($f);
				$r = fread($f, 4);

				$bits = decbin($this->unpackHeader($r));

				if ($frameoffset > $total) { return $this->getBadInfo(); }
			} while (!$this->isValidMP3Header($bits));
		} else { #more accurate with some VBRs
			$r = fread($f, 4);
			$bits = decbin($this->unpackHeader($r));

			while (!$this->isValidMP3Header($bits)) {
				if ($frameoffset > $total) { return $this->getBadInfo(); }
				fseek($f, ++$frameoffset);
				$r = fread($f, 4);
				$bits = decbin($this->unpackHeader($r));
			}
		}

		#$this->bits = $bits;
		$this->header_found = $frameoffset;
		$this->vbr = 0;
		$vbr = $this->getVBR($f, $bits[12], $bits[24] + $bits[25], $frameoffset);
		fclose($f);

		#TODO: vbr file size

		if ($bits[11] == 0) {
			$mpeg_ver = "2.5";
			$bitrates = array(
				'1'=>array(0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256, 0),
				'2'=>array(0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160, 0),
				'3'=>array(0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160, 0),
				);
		} else if ($bits[12] == 0) {
			$mpeg_ver = "2";
			$bitrates = array(
				'1'=>array(0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256, 0),
				'2'=>array(0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160, 0),
				'3'=>array(0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160, 0),
				);
		} else {
			$mpeg_ver = "1";
			$bitrates = array(
				'1'=>array(0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448, 0),
				'2'=>array(0, 32, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 384, 0),
				'3'=>array(0, 32, 40, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 0),
				);
		}

		$layers = array(array(0,3), array(2,1),);
		$layer = $layers[$bits[13]][$bits[14]];
		if ($layer == 0) return $this->getBadInfo();

		$bitrate = 0;
		if ($bits[16] == 1) $bitrate += 8;
		if ($bits[17] == 1) $bitrate += 4;
		if ($bits[18] == 1) $bitrate += 2;
		if ($bits[19] == 1) $bitrate += 1;
		if ($bitrate == 0) return $this->getBadInfo();

		$this->bitrate = $bitrates[$layer][$bitrate];

		$frequency = array(
			'1'=>array(
				'0'=>array(44100, 48000),
				'1'=>array(32000, 0),
				),
			'2'=>array(
				'0'=>array(22050, 24000),
				'1'=>array(16000, 0),
				),
			'2.5'=>array(
				'0'=>array(11025, 12000),
				'1'=>array(8000, 0),
				),
			 );
		$this->frequency = $frequency[$mpeg_ver][$bits[20]][$bits[21]];
		$mfs = $this->frequency / ($bits[12] ? 144000 : 72000);
		if ($mfs == 0) return $this->getBadInfo();
		$frames = (int)($vbr && $vbr['frames'] ? $vbr['frames'] : $this->filesize / $this->bitrate / $mfs);

		if ($vbr) {
			$this->vbr = 1;
			if ($vbr['scale']) $this->vbr_scale = $vbr['scale'];
			$this->bitrate = (int)($this->filesize / $frames * $mfs);
			if (!$this->bitrate) return $this->getBadInfo();
		}

		$s = -1;
		if ($this->bitrate != 0) {
			$s = ((8*($this->filesize))/1000) / $this->bitrate;
		}

		$this->length = (int)$s;	
		$this->time = sprintf('%.2d:%02d',floor($s/60),floor($s-(floor($s/60)*60)));
		$this->info = 1;
	}

	function getV2Header($fh) {
		fseek($fh, 0);
		$bytes = fread($fh, 3);

		if ($bytes != 'ID3') return false;

		#$bytes = fread($fh, 3);
		#get version
		$bytes = fread($fh, 2);
		$ver = unpack("C2",$bytes);
		$h->major_ver = $ver[1];
		$h->minor_ver = $ver[2];

		#get flags
		$bytes = fread($fh, 1);

		#get ID3v2 tag length from bytes 7-10
		$tag_size = 10;
		$bytes = fread($fh, 4);
		$temp = array_reverse(unpack("C4", $bytes));
		for($i=0; $i<=3; $i++) {
			$tag_size += $temp[$i] * pow(128,$i);
		}
		$h->tag_size = $tag_size;

		return $h;
	}

	function getVBR($fh, $id, $mode, &$offset) {
		$offset += 4;

		if ($id) {
			$offset += $mode == 2 ? 17 : 32;
		} else {
			$offset += $mode == 2 ? 9 : 17;
		}

		$bytes = $this->Seek($fh, $offset);

		if ($bytes != "Xing") return 0;

		$bytes = $this->Seek($fh, $offset);

		$vbr['flags'] = $this->unpackHeader($bytes);

		if ($vbr['flags'] & 1) {
			$bytes = $this->Seek($fh, $offset);
			$vbr['frames'] = $this->unpackHeader($bytes);
		}

		if ($vbr['flags'] & 2) {
			$bytes = $this->Seek($fh, $offset);
			$vbr['bytes'] = $this->unpackHeader($bytes);
		}

		if ($vbr['flags'] & 4) {
			$bytes = $this->Seek($fh, $offset, 100);
		}

		if ($vbr['flags'] & 8) {
			$bytes = $this->Seek($fh, $offset);
			$vbr['scale'] = $this->unpackHeader($bytes);
		} else {
			$vbr['scale'] = -1;
		}

		return $vbr;
	}

	function isValidMP3Header($bits) {
		if (strlen($bits) != 32) return false;
		if (substr_count(substr($bits,0,11),'0') != 0) return false;
		if ($bits[16] + $bits[17] + $bits[18] + $bits[19] == 4) return false;
		return true;
	}

	function getOgg($info, $tag) {
		$fh = fopen($this->file, 'rb');

		// Page 1 - Stream Header
		$h = null;
		if (!$this->getOggHeader($fh, $h)) { return $this->getBadInfo(); }

		if ($info) {
			$this->filesize = filesize($this->file);

			$data = fread($fh, 23);
			$offset = 0;

			$this->frequency = implode('',unpack('V1', substr($data, 5, 4)));
			$bitrate_average = 0;

			if (substr($data, 9, 4) !== chr(0xFF).chr(0xFF).chr(0xFF).chr(0xFF)) {
				$bitrate_max = implode('',unpack('V1', substr($data, 9, 4)));
			}
			if (substr($data, 13, 4) !== chr(0xFF).chr(0xFF).chr(0xFF).chr(0xFF)) {
				$bitrate_nominal = implode('',unpack('V1', substr($data, 13, 4)));
			}
			if (substr($data, 17, 4) !== chr(0xFF).chr(0xFF).chr(0xFF).chr(0xFF)) {
				$bitrate_min = implode('',unpack('V1', substr($data, 17, 4)));
			}
		}

		if ($tag) {
			// Page 2 - Comment Header
			if (!$this->getOggHeader($fh, $h)) { return $this->getBadInfo(); }
			$data = fread($fh, 16384);
			$offset = 0;
			$vendorsize = implode('',unpack('V1', substr($data, $offset, 4)));
			$offset += (4 + $vendorsize);
			$totalcomments = implode('',unpack('V1', substr($data, $offset, 4)));
			$offset += 4;

			for ($i = 0; $i < $totalcomments; $i++) {
				$commentsize = implode('',unpack('V1', substr($data, $offset, 4)));
				$offset += 4;
				$commentstring = substr($data, $offset, $commentsize);
				$offset += $commentsize;
				$comment = explode('=', $commentstring, 2);
				$comment[0] = strtolower($comment[0]);
				$this->$comment[0] = $comment[1];
			}
			$this->tag_version = "ogg";
			$this->tag = 1;
		}

		if ($info) {
			// Last Page - Number of Samples
			fseek($fh, max($this->filesize - 16384, 0), SEEK_SET);
			$LastChunkOfOgg = strrev(fread($fh, 16384));
			if ($LastOggSpostion = strpos($LastChunkOfOgg, 'SggO')) {
				fseek($fh, 0 - ($LastOggSpostion + strlen('SggO')), SEEK_END);
				if (!$this->getOggHeader($fh, $h)) { return $this->getBadInfo(); }
				$samples = $h->pcm;
				$bitrate_average = ($this->filesize * 8) / ($samples / $this->frequency);
			}

			if ($bitrate_average > 0) {
				$this->bitrate = $bitrate_average;
			} else if (isset($bitrate_nominal) && ($bitrate_nominal > 0)) {
				$this->bitrate = $bitrate_nominal;
			} else if (isset($bitrate_min) && isset($bitrate_max)) {
				$this->bitrate = ($bitrate_min + $bitrate_max) / 2;
			}

			$this->bitrate = (int) ($this->bitrate / 1000);
			$s = -1;
			if (isset($this->bitrate)) {
				$s = (float) (($this->filesize * 8) / $this->bitrate / 1000);
			}
			$this->time = sprintf('%.2d:%02d',floor($s/60),floor($s-(floor($s/60)*60)));
			$this->info = 1;
		}
		return true;
	}

	function getOggHeader(&$fh, &$h) {
		$baseoffset = ftell($fh);
		$data = fread($fh, 16384);
		$offset = 0;
		while ((substr($data, $offset++, 4) != 'OggS')) {
			if ($offset >= 10000) { return FALSE; }
		}

		$offset += 5;
		$h->pcm = implode('',unpack('V1', substr($data, $offset)));
		$offset += 20;
		$segments = implode('',unpack('C1', substr($data, $offset)));
		$offset += ($segments + 8);
		fseek($fh, $offset + $baseoffset, SEEK_SET);

		return true;
	}

	function getBadInfo() {
		$this->time = $this->bitrate = $this->frequency = 0;
		$this->filesize = filesize($this->file);
		return false;
	}

	function Seek($fh, &$offset, $n = 4) {
			fseek($fh, $offset);
			$bytes = fread($fh, $n);
			$offset += $n;
			return $bytes;
	}

	function unpackHeader($byte) {
		return implode('', unpack('N', $byte));
	}
}
?>
