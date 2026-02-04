<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'status_key' => 'pending',
                'status_label' => 'Pending (Menunggu Pembayaran)',
                'description' => 'Template untuk order yang menunggu pembayaran',
                'template' => "Halo [Nama],

Terima kasih sudah order di Arimbi Store! Pesananmu sudah kami terima nih.

Detail Pesanan:
[ListBarang]
Total: [Total]

Silakan lakukan pembayaran ke:
🏦 BCA - 1234567890 (A/N Arimbi)

Mohon kirimkan bukti transfernya di sini ya kalau sudah. Pesananmu akan segera kami proses setelah pembayaran terkonfirmasi. Makasih! 🙏",
            ],
            [
                'status_key' => 'processing',
                'status_label' => 'Processing (Sedang Disiapkan)',
                'description' => 'Template untuk order yang sedang diproses/dikemas',
                'template' => "Yeay, Pembayaran Diterima! 🥳

Halo [Nama], pesananmu sedang kami siapkan dan dibungkus rapi. Kami pastikan produk kamu dalam kondisi terbaik sebelum dikirim.

Detail Pesanan:
[ListBarang]
Total: [Total]

Mohon ditunggu ya, kami akan kabari lagi kalau paketnya sudah diserahkan ke kurir. 📦✨",
            ],
            [
                'status_key' => 'shipped',
                'status_label' => 'Shipped (Dalam Pengiriman)',
                'description' => 'Template untuk order yang sudah dikirim',
                'template' => "Paketmu Sedang Meluncur! 🚀

Pesananmu sudah diserahkan ke kurir nih.

Nomor Resi: [NomorResi]
Invoice: [NomorOrder]

Pantau terus perjalanannya ya. Semoga cepat sampai dan selamat sampai tujuan! 💨",
            ],
            [
                'status_key' => 'completed',
                'status_label' => 'Completed (Selesai)',
                'description' => 'Template untuk order yang sudah selesai',
                'template' => "Paket Sudah Sampai! 🎁

Halo [Nama], menurut catatan kami, pesananmu sudah diterima ya. Semoga kamu suka dengan produknya!

Kalau berkenan, jangan lupa tag kami di Instagram atau kasih testimoni ya. Review kamu sangat berarti buat kami. Sampai belanja lagi di Arimbi Store! 😊👋",
            ],
            [
                'status_key' => 'cancelled',
                'status_label' => 'Cancelled (Dibatalkan)',
                'description' => 'Template untuk order yang dibatalkan',
                'template' => "Informasi Pembatalan Pesanan

Mohon maaf [Nama], pesananmu dengan kode #[NomorOrder] terpaksa kami batalkan.

Jangan ragu buat tanya-tanya lagi kalau ada produk lain yang kamu incar ya. Semoga bisa berjodoh di orderan berikutnya! 🙏",
            ],
        ];

        foreach ($templates as $template) {
            MessageTemplate::updateOrCreate(
                ['status_key' => $template['status_key']],
                $template
            );
        }
    }
}
