<?php

/**
 * File ini:
 *
 * Model untuk modul database
 *
 * donjo-app/models/migrations/Migrasi_fitur_premium_2107.php
 *
 */

/**
 *
 * File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2021 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:

 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.

 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package   OpenSID
 * @author    Tim Pengembang OpenDesa
 * @copyright Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright Hak Cipta 2016 - 2021 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license   http://www.gnu.org/licenses/gpl.html GPL V3
 * @link      https://github.com/OpenSID/OpenSID
 */

class Migrasi_fitur_premium_2107 extends MY_Model
{
	public function up()
	{
		log_message('error', 'Jalankan ' . get_class($this));
		$hasil = TRUE;
		$hasil = $hasil && $this->migrasi_2021061652($hasil);
		$hasil = $hasil && $this->migrasi_2021062051($hasil);

		status_sukses($hasil);
		return $hasil;
	}

	protected function migrasi_2021061652($hasil)
	{
		// Ubah nilai default foto pada tabel user
		$fields = [
			'foto' => [
				'name' => 'foto',
				'type' => 'VARCHAR',
				'constraint' => 100,
				'default' => 'kuser.png',
			],
		];

		$hasil = $hasil && $this->dbforge->modify_column('user', $fields);

		return $hasil;
	}
  
  protected function migrasi_2021062051($hasil)
	{
		// Tambahkan id_cluster pada tweb_keluarga yg null
		$query = "
			update tweb_keluarga as k, 
				(select t.* from 
				   (select id, id_kk, id_cluster from tweb_penduduk where id_kk in 
				     (select id from tweb_keluarga where id_cluster is null)
				   ) t
				) as p
				set k.id_cluster = p.id_cluster
				where k.id = p.id_kk
		";

		$hasil = $hasil && $this->db->query($query);

		// Perbaiki struktur table tweb_keluarga field id_cluster tdk boleh null
		$fields = [
			'id_cluster' => [
				'name' => 'id_cluster',
				'type' => 'INT',
				'constraint' => 11,
				'null' => FALSE,
			],
		];

		$hasil = $hasil && $this->dbforge->modify_column('tweb_keluarga', $fields);
		
		return $hasil;
	}

}
