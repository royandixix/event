
document.addEventListener("DOMContentLoaded", function () {
    const status = new URLSearchParams(window.location.search).get("status");

    if (status === "success") {
        Swal.fire({
            icon: "success",
            title: "🎉 Pendaftaran Peserta Berhasil!",
            html: '<p style="font-size:15px;color:#555;">Data peserta telah berhasil dikirim.<br>Terima kasih sudah mendaftar!</p>',
            confirmButtonText: "Lanjutkan 🚀",
            confirmButtonColor: "#2563eb",
            background: "#f0f9ff",
            backdrop: `
                rgba(0,0,0,0.4)
                url("https://media.giphy.com/media/5GoVLqeAOo6PK/giphy.gif")
                center top
                no-repeat
            `,
            showClass: {
                popup: "animate__animated animate__zoomIn",
            },
            hideClass: {
                popup: "animate__animated animate__fadeOutUp",
            },
        });
    } else if (status === "error") {
        Swal.fire({
            icon: "error",
            title: "❌ Pendaftaran Peserta Gagal",
            html: '<p style="font-size:15px;color:#555;">Terjadi kesalahan saat mengirim data.<br>Silakan coba lagi.</p>',
            confirmButtonText: "Coba Lagi 🔄",
            confirmButtonColor: "#d33",
            background: "#fff5f5",
            backdrop: `
                rgba(0,0,0,0.4)
                url("https://media.giphy.com/media/jWexOOlYe241y/giphy.gif")
                center top
                no-repeat
            `,
            showClass: {
                popup: "animate__animated animate__shakeX",
            },
            hideClass: {
                popup: "animate__animated animate__fadeOutDown",
            },
        });
    }
});



document.querySelector('form').addEventListener('submit', function (e) {
    const namaPeserta = document.querySelector('input[name="nama_peserta"]').value.trim();
    const namaTim = document.querySelector('input[name="nama_tim"]').value.trim();
    const whatsapp = document.querySelector('input[name="whatsapp"]').value.trim();

    // Slot id di form kamu tidak ada, jadi bisa dilewati atau tambahkan jika diperlukan
    // const slotId = document.getElementById("slot_id")?.value.trim() || "";

    if (!namaPeserta || !namaTim || !whatsapp) {
        e.preventDefault();
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle text-yellow-400 text-5xl"></i>',
            title: '<span style="color:#1e3a8a;">Form Belum Lengkap!</span>',
            html: `
                <div style="text-align:left; font-size:14px; color:#374151;">
                    <p class="mb-2">Harap lengkapi data berikut sebelum melanjutkan:</p>
                    <ul class="list-disc list-inside" style="line-height:1.6;">
                        <li>Nama Peserta</li>
                        <li>Nama Tim</li>
                        <li>Nomor WhatsApp</li>
                    </ul>
                </div>
            `,
            background: "linear-gradient(135deg, #ffffff, #f3f4f6)",
            showConfirmButton: true,
            confirmButtonColor: "#2563eb",
            confirmButtonText: '<i class="fas fa-check-circle"></i> Oke, mengerti!',
            customClass: {
                popup: "rounded-2xl shadow-lg",
                confirmButton: "px-5 py-2 font-semibold",
            },
            showClass: { popup: "animate__animated animate__fadeInUp animate__faster" },
            hideClass: { popup: "animate__animated animate__fadeOutDown animate__faster" },
        });
    }
});
