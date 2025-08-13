
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

