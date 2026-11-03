<?php

namespace App\Support;

class LearningContent
{
    public static function lessons(): array
    {
        return [
            ['id'=>'jaringan','unit'=>1,'title'=>'Jaringan & topologi','eyebrow'=>'01 · DASAR JARINGAN','minutes'=>5,'diagram'=>'jaringan','content'=>[
                'Jaringan komputer adalah sekumpulan perangkat yang saling terhubung untuk bertukar data dan berbagi sumber daya. Perangkat berkomunikasi melalui media kabel, serat optik, atau gelombang radio, dengan aturan komunikasi yang disebut protokol.',
                'Topologi jaringan menjelaskan bagaimana perangkat dan jalur komunikasi disusun. Topologi fisik menunjukkan sambungan yang benar-benar dipasang; topologi logis menunjukkan bagaimana data mengalir. Pilihan topologi memengaruhi biaya, kinerja, kemudahan pengembangan, dan cara gangguan dilacak.',
                'Perangkat akhir seperti PC dan printer dihubungkan melalui media transmisi. Switch meneruskan frame di LAN berdasarkan alamat MAC, sedangkan router menghubungkan jaringan IP yang berbeda.',
            ],'takeaways'=>['Jaringan membuat perangkat dapat bertukar data dan berbagi sumber daya.','Topologi adalah susunan hubungan fisik atau aliran logis antarperangkat.','Switch lazim dipakai di LAN; router menghubungkan jaringan yang berbeda.'],'terms'=>['jaringan komputer','topologi','switch','router']],
            ['id'=>'bus','unit'=>1,'title'=>'Topologi Bus','eyebrow'=>'02 · TOPOLOGI DASAR','minutes'=>4,'diagram'=>'bus','content'=>[
                'Pada topologi Bus, seluruh perangkat berbagi satu kabel utama (backbone). Data merambat sepanjang kabel dan dapat diterima perangkat lain. Terminator dipasang di kedua ujung backbone untuk menyerap sinyal agar tidak memantul kembali.',
                'Bus hemat kabel dan relatif sederhana untuk jaringan kecil. Namun, backbone menjadi titik kegagalan tunggal: kabel putus dapat mengganggu semua perangkat. Ketika banyak perangkat mengirim bersamaan, peluang tabrakan data meningkat dan pelacakan gangguan lebih sulit.',
                'Bus pernah dipakai pada Ethernet koaksial lama. Saat ini, Ethernet LAN umumnya menggunakan topologi fisik star dengan switch karena lebih mudah dikelola dan dikembangkan.',
            ],'takeaways'=>['Satu backbone dipakai bersama oleh seluruh node.','Terminator mengurangi pantulan sinyal di kedua ujung kabel.','Gangguan backbone berdampak luas; bus kurang sesuai untuk LAN modern.'],'terms'=>['backbone','terminator','tabrakan data']],
            ['id'=>'star','unit'=>1,'title'=>'Topologi Star','eyebrow'=>'03 · TOPOLOGI DASAR','minutes'=>4,'diagram'=>'star','content'=>[
                'Pada topologi Star, setiap perangkat memiliki sambungan tersendiri ke perangkat pusat, biasanya switch. Switch mempelajari alamat MAC dan meneruskan frame ke port tujuan, sehingga perangkat tidak berbagi satu kabel backbone seperti pada bus.',
                'Penambahan node dan pelacakan masalah relatif mudah: kerusakan satu kabel cabang umumnya hanya memutus satu perangkat. Sebaliknya, kegagalan switch pusat dapat memutus komunikasi seluruh node yang terhubung. Star memerlukan kabel lebih banyak daripada bus.',
                'Star adalah topologi fisik yang umum pada LAN sekolah dan kantor. Jaringan dapat diperluas dengan menghubungkan beberapa switch, dengan perencanaan loop dan redundansi yang tepat.',
            ],'takeaways'=>['Semua sambungan menuju switch pusat.','Kerusakan satu kabel cabang biasanya tidak memutus node lain.','Switch pusat adalah titik penting yang perlu dilindungi.'],'terms'=>['switch','node','star']],
            ['id'=>'ring','unit'=>1,'title'=>'Topologi Ring','eyebrow'=>'04 · TOPOLOGI DASAR','minutes'=>4,'diagram'=>'ring','content'=>[
                'Dalam topologi Ring, setiap node terhubung dengan dua tetangga sehingga membentuk lintasan melingkar. Pada ring satu arah, data diteruskan dari node ke node hingga mencapai tujuan. Token passing adalah salah satu metode akses yang pernah digunakan: hanya pemegang token yang boleh mengirim.',
                'Jika ring tidak memiliki jalur cadangan, satu putus sambungan dapat mengganggu seluruh cincin. Ring terkelola dapat memiliki jalur ganda atau mekanisme pemulihan untuk meningkatkan ketahanan, tetapi menambah kompleksitas.',
                'Contoh historisnya adalah Token Ring. Prinsip lintasan cincin juga dipakai pada beberapa jaringan metro dan cincin serat optik dengan proteksi, meskipun detail teknologinya berbeda.',
            ],'takeaways'=>['Node memiliki dua tetangga dan membentuk lintasan tertutup.','Token passing mengatur giliran kirim pada desain tertentu.','Jalur cadangan dapat membantu melindungi ring dari putusnya tautan.'],'terms'=>['token passing','ring','lintasan']],
            ['id'=>'mesh','unit'=>1,'title'=>'Topologi Mesh','eyebrow'=>'05 · TOPOLOGI DASAR','minutes'=>4,'diagram'=>'mesh','content'=>[
                'Pada full mesh, setiap perangkat memiliki koneksi langsung ke semua perangkat lainnya. Pada partial mesh, hanya beberapa pasangan yang memiliki koneksi langsung; rute lain memakai perangkat perantara. Untuk n node, full mesh memerlukan n(n−1)/2 sambungan dua arah.',
                'Jalur alternatif membuat mesh tangguh terhadap kegagalan tautan, asalkan tersedia rute lain dan perangkat mampu mengalihkan trafik. Imbalannya adalah biaya kabel, port, konfigurasi, dan pengelolaan yang meningkat.',
                'Mesh nirkabel dapat membentuk rute antar-access point atau antar-node; topologi logis yang terbentuk bergantung pada protokol. Mesh cocok untuk kebutuhan redundansi tinggi, misalnya sebagian jaringan backbone atau jaringan sensor.',
            ],'takeaways'=>['Full mesh menghubungkan setiap pasangan node secara langsung.','Banyak jalur memberi redundansi bila ada mekanisme alih rute.','Biaya dan jumlah sambungan full mesh tumbuh cepat saat node bertambah.'],'terms'=>['full mesh','partial mesh','redundansi']],
            ['id'=>'tree','unit'=>1,'title'=>'Topologi Tree','eyebrow'=>'06 · TOPOLOGI DASAR','minutes'=>4,'diagram'=>'tree','content'=>[
                'Topologi Tree menyusun jaringan secara bertingkat menyerupai cabang pohon. Satu node akar atau switch distribusi terhubung ke lapisan berikutnya, lalu ke switch akses dan perangkat akhir. Bentuk ini menggabungkan beberapa segmen star secara hierarkis.',
                'Struktur bertingkat memudahkan pengelompokan jaringan per lantai, ruang, atau bagian organisasi. Perluasan dilakukan pada cabang terkait. Jika perangkat atau tautan di tingkat atas gagal, cabang di bawahnya dapat kehilangan akses ke jaringan lain.',
                'Desain kampus sering memakai lapisan akses, distribusi, dan inti. Perencanaan kapasitas uplink serta jalur redundan pada lapisan penting membantu menjaga kinerja dan ketersediaan.',
            ],'takeaways'=>['Tree mengatur beberapa segmen star dalam lapisan hierarkis.','Mudah dikembangkan dan dikelola per cabang.','Kegagalan di lapisan atas dapat berdampak pada banyak cabang.'],'terms'=>['hierarki','lapisan akses','uplink']],
            ['id'=>'hybrid','unit'=>1,'title'=>'Topologi Hybrid','eyebrow'=>'07 · TOPOLOGI DASAR','minutes'=>4,'diagram'=>'hybrid','content'=>[
                'Topologi Hybrid menggabungkan dua atau lebih bentuk topologi dalam satu jaringan, misalnya kelompok LAN star yang terhubung melalui backbone, atau beberapa star yang disusun bertingkat seperti tree. Bentuknya dipilih untuk mengikuti kebutuhan lokasi, kapasitas, dan anggaran.',
                'Hybrid dapat memanfaatkan kelebihan masing-masing rancangan. Sebagai contoh, star di setiap lab mudah ditangani, sementara koneksi redundan antarswitch membantu menjaga jalur antarbagian. Namun, desain dan dokumentasinya lebih kompleks; jenis perangkat dan konfigurasi harus tetap kompatibel.',
                'Tidak ada satu bentuk yang selalu paling baik. Pilih topologi berdasarkan ukuran jaringan, kebutuhan ketersediaan, biaya pemasangan, kemudahan pemeliharaan, dan rencana pertumbuhan.',
            ],'takeaways'=>['Hybrid menggabungkan beberapa topologi sesuai kebutuhan.','Fleksibel, tetapi memerlukan perencanaan dan dokumentasi yang rapi.','Pertimbangkan biaya, pertumbuhan, kinerja, dan dampak gangguan.'],'terms'=>['hybrid','skalabilitas','dokumentasi']],
            ['id'=>'ipv4','unit'=>2,'title'=>'Mengenal alamat IPv4','eyebrow'=>'01 · PENGALAMATAN IP','minutes'=>5,'diagram'=>'ipv4','content'=>[
                'Alamat IP adalah alamat logis yang membantu mengidentifikasi antarmuka pada jaringan IP dan mengarahkan paket ke tujuan. IPv4 menggunakan 32 bit, biasanya ditulis sebagai empat bilangan desimal bertitik, contohnya 192.168.10.25.',
                'Setiap bilangan (oktet) mewakili 8 bit dengan bobot 128, 64, 32, 16, 8, 4, 2, dan 1. Nilai oktet berkisar 0–255. Alamat tersebut bersama prefix menentukan bagian jaringan dan bagian host.',
                'Perangkat biasanya memperoleh alamat secara manual atau melalui DHCP. Alamat IPv4 pada antarmuka harus sesuai dengan subnet lokal; konfigurasi umum juga mencakup prefix/subnet mask, gateway default, dan DNS.',
            ],'takeaways'=>['IPv4 terdiri dari 32 bit yang ditulis sebagai empat oktet.','Setiap oktet memiliki nilai desimal 0 sampai 255.','Alamat dan prefix dipakai bersama untuk menentukan subnet.'],'terms'=>['IPv4','oktet','DHCP','gateway']],
            ['id'=>'network-host','unit'=>2,'title'=>'Network ID & Host ID','eyebrow'=>'02 · PENGALAMATAN IP','minutes'=>5,'diagram'=>'network-host','content'=>[
                'Prefix jaringan (misalnya /24) menunjukkan banyaknya bit paling kiri yang menjadi bagian jaringan. Bit sisanya mengidentifikasi antarmuka dalam subnet tersebut. Batas itu dapat berada di tengah oktet; karena itu jangan selalu menganggap satu oktet penuh sebagai Network ID.',
                'Dengan 192.168.10.25/24, 24 bit pertama adalah bagian prefix dan 8 bit terakhir adalah bagian host. Network address subnetnya 192.168.10.0. Pada subnet mask yang lebih panjang seperti /26, batas host berada 2 bit ke dalam oktet keempat.',
                'Alamat host biasanya tidak memakai alamat network (semua bit host 0) atau broadcast (semua bit host 1) pada subnet IPv4 biasa. Prefix /31 memiliki aturan khusus untuk tautan point-to-point, sedangkan /32 menunjuk satu alamat.',
            ],'takeaways'=>['Prefix /n menandai n bit paling kiri sebagai bagian jaringan.','Batas prefix dapat melintasi oktet.','Alamat network dan broadcast punya fungsi khusus pada subnet biasa.'],'terms'=>['prefix','Network ID','Host ID','broadcast']],
            ['id'=>'kelas','unit'=>2,'title'=>'Kelas alamat A–E','eyebrow'=>'03 · PENGALAMATAN IP','minutes'=>4,'diagram'=>'kelas','content'=>[
                'Pengalamatan classful membagi alamat berdasarkan oktet pertama: A (1–126), B (128–191), C (192–223), D (224–239), dan E (240–255). 0 dan 127 memiliki penggunaan khusus; 127.0.0.0/8 dipakai untuk loopback.',
                'Dalam model classful lama, mask bawaan kelas A, B, dan C berturut-turut adalah /8, /16, dan /24. Kelas D digunakan untuk multicast, bukan untuk alamat host unicast biasa. Kelas E dicadangkan/eksperimental dan tidak dialokasikan sebagai alamat host umum.',
                'Jaringan modern memakai CIDR: prefix ditentukan eksplisit dan tidak bergantung pada kelas. Kelas tetap dipelajari untuk memahami sejarah IPv4 dan mengenali rentang, tetapi jangan menggunakannya untuk menghitung subnet modern.',
            ],'takeaways'=>['Kelas A–C dahulu digunakan untuk unicast dengan mask bawaan berbeda.','Kelas D untuk multicast; E dicadangkan/eksperimental.','CIDR menggantikan pembagian classful untuk perancangan jaringan modern.'],'terms'=>['classful','multicast','loopback','CIDR']],
            ['id'=>'publik-privat','unit'=>2,'title'=>'IP publik & privat','eyebrow'=>'04 · PENGALAMATAN IP','minutes'=>4,'diagram'=>'publik-privat','content'=>[
                'Alamat IPv4 publik dapat dirutekan secara global jika diumumkan dan kebijakan jaringan mengizinkannya. Alamat privat dipakai ulang di jaringan internal dan tidak dirutekan di Internet publik; perangkat biasanya mengakses Internet melalui router yang melakukan NAT.',
                'Rentang privat menurut RFC 1918 adalah 10.0.0.0/8, 172.16.0.0/12 (172.16.0.0–172.31.255.255), dan 192.168.0.0/16. Alamat seperti 169.254.0.0/16 adalah link-local dan bukan salah satu rentang RFC 1918.',
                'NAT membantu banyak perangkat privat berbagi satu atau beberapa alamat publik, tetapi NAT sendiri bukan pengganti firewall atau kebijakan keamanan. Alamat publik juga tidak otomatis berarti layanan aman atau terbuka untuk semua orang.',
            ],'takeaways'=>['Alamat privat tidak dirutekan pada Internet publik.','Tiga rentang privat: 10/8, 172.16/12, dan 192.168/16.','NAT menerjemahkan alamat; keamanan tetap memerlukan kontrol tersendiri.'],'terms'=>['alamat publik','alamat privat','NAT','RFC 1918']],
            ['id'=>'mask-cidr','unit'=>2,'title'=>'Subnet mask & CIDR','eyebrow'=>'05 · PENGALAMATAN IP','minutes'=>5,'diagram'=>'mask-cidr','content'=>[
                'Subnet mask IPv4 memiliki bit 1 berurutan dari kiri untuk menandai prefix jaringan, lalu bit 0 untuk bagian host. Prefix /24 setara dengan 255.255.255.0; /26 setara dengan 255.255.255.192.',
                'CIDR menulis panjang prefix setelah garis miring, contohnya 192.168.10.0/26. Dengan prefix /26, terdapat 32−26=6 bit host dan 2⁶=64 alamat per subnet. Pada subnet biasa, 62 alamat dapat dipakai host karena network dan broadcast tidak diberikan ke host.',
                'Prefix lebih panjang menghasilkan subnet lebih kecil. Gunakan prefix yang cukup untuk jumlah host dan sisakan ruang untuk pertumbuhan. Semua perangkat dalam subnet harus menggunakan subnet mask yang konsisten.',
            ],'takeaways'=>['Bit 1 mask berurutan menentukan prefix jaringan.','/26 memiliki 64 alamat total atau 62 alamat host pada subnet biasa.','Notasi CIDR menyatakan panjang prefix secara ringkas.'],'terms'=>['subnet mask','CIDR','prefix']],
            ['id'=>'subnetting','unit'=>2,'title'=>'Subnetting dasar','eyebrow'=>'06 · PENGALAMATAN IP','minutes'=>6,'diagram'=>'subnetting','content'=>[
                'Subnetting membagi satu blok IP menjadi jaringan yang lebih kecil. Contoh: 192.168.10.0/24 dapat dibagi menjadi empat subnet /26. Prefix bertambah 2 bit sehingga ada 2²=4 subnet; setiap /26 memuat 64 alamat.',
                'Ukuran blok pada oktet yang berubah adalah 256 dikurangi nilai mask di oktet itu. Untuk /26, mask 255.255.255.192, jadi ukuran blok 256−192=64. Network subnet dimulai pada 0, 64, 128, dan 192.',
                'Subnet 192.168.10.0/26 memiliki rentang alamat 192.168.10.0–192.168.10.63. Network address adalah .0, broadcast .63, dan host biasa yang dapat dipakai .1–.62. Selalu cocokkan network, broadcast, kapasitas host, dan prefix sebelum mengonfigurasi perangkat.',
            ],'takeaways'=>['Menambah prefix 2 bit membentuk empat subnet berukuran sama.','Ukuran blok /26 adalah 64 alamat.','Untuk 192.168.10.0/26, host biasa .1–.62; broadcast .63.'],'terms'=>['subnetting','ukuran blok','network address','broadcast address']],
        ];
    }

    public static function quiz(): array
    {
        return [
            ['q'=>'Pada topologi Star, perangkat pusat yang lazim meneruskan frame berdasarkan alamat MAC adalah …','options'=>['Repeater','Switch','Terminator','Modem'],'answer'=>1,'explain'=>'Switch mempelajari alamat MAC dan meneruskan frame melalui port menuju tujuan. Router bekerja pada jaringan IP yang berbeda.','lesson'=>'star'],
            ['q'=>'Apa fungsi terminator di kedua ujung kabel utama topologi Bus?','options'=>['Memberi alamat IP','Menguatkan daya listrik','Menyerap sinyal agar tidak memantul','Membagi domain broadcast'],'answer'=>2,'explain'=>'Terminator menyerap sinyal di ujung backbone sehingga pantulan sinyal dapat dikurangi.','lesson'=>'bus'],
            ['q'=>'Berapa jumlah bit pada satu alamat IPv4?','options'=>['8 bit','16 bit','32 bit','64 bit'],'answer'=>2,'explain'=>'IPv4 terdiri dari 32 bit, ditulis dalam empat oktet yang masing-masing berisi 8 bit.','lesson'=>'ipv4'],
            ['q'=>'Rentang alamat privat RFC 1918 yang benar adalah …','options'=>['172.0.0.0/8','172.16.0.0/12','172.32.0.0/12','169.254.0.0/16'],'answer'=>1,'explain'=>'Rentang tengah privat adalah 172.16.0.0 sampai 172.31.255.255 (172.16.0.0/12).','lesson'=>'publik-privat'],
            ['q'=>'Prefix /26 memiliki berapa alamat IPv4 total dalam satu subnet?','options'=>['32','64','128','256'],'answer'=>1,'explain'=>'Jumlah bit host = 32−26 = 6, sehingga total alamat 2⁶ = 64.','lesson'=>'mask-cidr'],
            ['q'=>'Network address untuk host 192.168.10.25/26 adalah …','options'=>['192.168.10.0','192.168.10.25','192.168.10.63','192.168.10.64'],'answer'=>0,'explain'=>'Blok /26 berukuran 64. Alamat .25 berada pada subnet .0 sampai .63; network address-nya .0.','lesson'=>'subnetting'],
            ['q'=>'Kelas IPv4 historis yang digunakan untuk multicast adalah …','options'=>['A','B','D','E'],'answer'=>2,'explain'=>'Rentang classful kelas D adalah 224.0.0.0–239.255.255.255 dan digunakan untuk multicast.','lesson'=>'kelas'],
            ['q'=>'Jika sebuah subnet biasa memiliki prefix /27, berapa host yang umumnya dapat dipakai?','options'=>['30','31','32','62'],'answer'=>0,'explain'=>'Prefix /27 menyisakan 5 bit host: 32 alamat total, dikurangi network dan broadcast menjadi 30 host. /31 adalah pengecualian point-to-point.','lesson'=>'subnetting'],
        ];
    }

    public static function pairs(): array
    {
        return [
            ['term'=>'Switch','meaning'=>'Meneruskan frame di LAN berdasarkan alamat MAC'],
            ['term'=>'Router','meaning'=>'Menghubungkan jaringan IP yang berbeda'],
            ['term'=>'Terminator','meaning'=>'Mengurangi pantulan sinyal pada kabel bus'],
            ['term'=>'Prefix /24','meaning'=>'24 bit paling kiri menjadi bagian jaringan'],
        ];
    }

    public static function glossary(): array
    {
        return [
            ['term'=>'Alamat IP','definition'=>'Alamat logis yang mengidentifikasi antarmuka pada jaringan IP dan membantu pengiriman paket.'],
            ['term'=>'Backbone','definition'=>'Jalur utama yang menghubungkan bagian-bagian atau perangkat distribusi dalam jaringan.'],
            ['term'=>'Broadcast','definition'=>'Pengiriman ke semua host dalam satu subnet; alamat broadcast IPv4 biasa memakai semua bit host bernilai 1.'],
            ['term'=>'CIDR','definition'=>'Cara menyatakan alamat jaringan beserta panjang prefix, misalnya 192.168.10.0/24.'],
            ['term'=>'DHCP','definition'=>'Protokol untuk membagikan konfigurasi IP secara otomatis kepada klien.'],
            ['term'=>'Gateway default','definition'=>'Router yang menjadi jalur keluar menuju jaringan IP lain ketika tujuan tidak berada di subnet lokal.'],
            ['term'=>'Host ID','definition'=>'Bit pada alamat IPv4 di luar prefix yang membedakan antarmuka dalam subnet.'],
            ['term'=>'NAT','definition'=>'Penerjemahan alamat IP pada batas jaringan, umumnya antara alamat privat dan publik.'],
            ['term'=>'Network address','definition'=>'Alamat pertama subnet biasa; seluruh bit host bernilai 0 dan mewakili subnet itu sendiri.'],
            ['term'=>'Network ID','definition'=>'Bagian prefix pada alamat yang mengidentifikasi jaringan atau subnet.'],
            ['term'=>'Oktet','definition'=>'Kelompok 8 bit pada IPv4; ditulis sebagai satu angka desimal 0–255.'],
            ['term'=>'Prefix','definition'=>'Jumlah bit paling kiri yang menjadi bagian jaringan, ditulis /n.'],
            ['term'=>'Redundansi','definition'=>'Jalur atau komponen cadangan agar layanan tetap dapat berjalan saat terjadi gangguan.'],
            ['term'=>'Router','definition'=>'Perangkat yang meneruskan paket antarjaringan IP berdasarkan informasi perutean.'],
            ['term'=>'Switch','definition'=>'Perangkat LAN yang meneruskan frame berdasarkan alamat MAC.'],
            ['term'=>'Subnet mask','definition'=>'Mask IPv4 yang memakai bit 1 untuk prefix jaringan dan bit 0 untuk bagian host.'],
            ['term'=>'Terminator','definition'=>'Komponen di ujung kabel bus untuk menyerap sinyal dan mengurangi pantulan.'],
            ['term'=>'Topologi','definition'=>'Pola susunan hubungan fisik atau alur logis antarperangkat jaringan.'],
        ];
    }
}
