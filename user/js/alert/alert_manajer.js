document.addEventListener("DOMContentLoaded", function () {
  const status = new URLSearchParams(window.location.search).get("status");

  if (status === "success") {
    Swal.fire({
      icon: "success",
      title: "🎉 Pendaftaran Berhasil!",
      html: '<p style="font-size:15px;color:#555;">Data manajer telah berhasil dikirim.<br>Terima kasih sudah mendaftar!</p>',
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
      title: "❌ Pendaftaran Gagal",
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

// user/js/alert/alert_paddock.js

// Fungsi memilih slot
function pilihSlot(id, el) {
  // Hapus highlight dari semua slot
  document.querySelectorAll(".slot-item").forEach((btn) => {
    btn.classList.remove(
      "ring-4",
      "ring-yellow-300",
      "scale-105",
      "bg-green-500"
    );
  });

  // Tambahkan highlight ke slot terpilih
  el.classList.add(
    "ring-4",
    "ring-yellow-300",
    "scale-105",
    "bg-green-500",
    "transition-transform",
    "duration-300",
    "ease-out"
  );

  // Set ID slot di input hidden
  document.getElementById("slot_id").value = id;
}

// Validasi form sebelum submit
document.getElementById("paddockForm").addEventListener("submit", function (e) {
  const namaPesanan = document
    .querySelector('input[name="nama_pesanan"]')
    .value.trim();
  const namaTim = document.querySelector('input[name="nama_tim"]').value.trim();
  const nomorWa = document.querySelector('input[name="nomor_wa"]').value.trim();
  const slotId = document.getElementById("slot_id").value.trim();

  if (!namaPesanan || !namaTim || !nomorWa || !slotId) {
    e.preventDefault();
    Swal.fire({
      iconHtml:
        '<i class="fas fa-exclamation-circle text-yellow-400 text-5xl"></i>',
      title: '<span style="color:#1e3a8a;">Form Belum Lengkap!</span>',
      html: `
                <div style="text-align:left; font-size:14px; color:#374151;">
                    <p class="mb-2">Harap lengkapi data berikut sebelum melanjutkan:</p>
                    <ul class="list-disc list-inside" style="line-height:1.6;">
                        <li>Nama Pesanan</li>
                        <li>Nama Tim</li>
                        <li>Nomor WhatsApp</li>
                        <li>Pilih Slot Paddock</li>
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
      showClass: {
        popup: "animate__animated animate__fadeInUp animate__faster",
      },
      hideClass: {
        popup: "animate__animated animate__fadeOutDown animate__faster",
      },
    });
  }
});

// Alert setelah pendaftaran berhasil
document.addEventListener("DOMContentLoaded", function () {
  if (typeof window.alertData !== "undefined") {
    let htmlContent = `<div style="text-align:center;">`;

    if (window.alertData.gambar_bank) {
      htmlContent += `
                <img src="${window.alertData.gambar_bank}" 
                style="
                    width: 320px;
                    max-width: 100%;
                    margin-bottom: 12px;
                    border-radius: 10px;
                    box-shadow: 0 6px 15px rgba(0,0,0,0.25);
                ">
                <br>`;
    }

    htmlContent += `
            <b>Nomor Invoice:</b> ${window.alertData.nomor_invoice}<br>
            <b>Total Harga:</b> Rp ${window.alertData.total_harga}<br>
            <b>Kode Unik:</b> ${window.alertData.kode_unik}<br>
            <b>Total Transfer:</b> Rp ${window.alertData.total_transfer}<br>
            <b>Bank Tujuan:</b> ${window.alertData.bank_tujuan}<br>
            <b>No Rekening:</b> ${window.alertData.no_rekening}<br>
            <b>Nama Pemilik Rekening:</b> ${window.alertData.nama_pemilik_rekening}<br>
        </div>`;

    Swal.fire({
      title: "Pendaftaran Berhasil!",
      html: htmlContent,
      icon: "success",
      width: 500, // ukuran popup normal
      confirmButtonColor: "#2563eb",
      confirmButtonText: "Oke, Mengerti!",
    }).then(() => {
      window.location.href = window.alertData.redirect;
    });
  }
});

// user/js/alert_manajer.js

/**
 * Inisialisasi dynamic class input (Tambah & Hapus)
 * @param {string} wrapperId - ID container kelas
 * @param {string} tambahBtnId - ID tombol tambah
 */
function initKelasDynamic(wrapperId, tambahBtnId) {
  const wrapper = document.getElementById(wrapperId);
  const tambahBtn = document.getElementById(tambahBtnId);

  // Event klik tombol tambah
  tambahBtn.addEventListener("click", () => {
    const firstItem = wrapper.querySelector(".kelas-item");
    const clone = firstItem.cloneNode(true);

    // Reset semua input di clone
    clone.querySelectorAll("input").forEach((input) => (input.value = ""));

    wrapper.appendChild(clone);
  });

  // Event delegation: tangani klik hapus pada seluruh wrapper
  wrapper.addEventListener("click", (e) => {
    if (e.target.closest(".hapusKelas")) {
      const items = wrapper.querySelectorAll(".kelas-item");
      const itemToRemove = e.target.closest(".kelas-item");

      if (items.length > 1) {
        itemToRemove.remove();
      } else {
        Swal.fire({
          icon: "warning",
          title: "Oops...",
          text: "Minimal harus ada 1 kelas!",
          confirmButtonColor: "#3085d6",
          confirmButtonText: "OK",
        });
      }
    }
  });
}

// Inisialisasi untuk masing-masing wrapper
document.addEventListener("DOMContentLoaded", () => {
  initKelasDynamic("kelas-wrapper", "tambahKelas");
  initKelasDynamic("kelas-wrapper-manajer", "tambahKelasManajer");

  // Form validation sederhana
  function setupValidation(formSelector) {
    const form = document.querySelector(formSelector);
    const submitBtn = form.querySelector('button[type="submit"]');

    submitBtn.addEventListener("click", (e) => {
      e.preventDefault();

      const requiredFields = form.querySelectorAll(
        "input[required], select[required]"
      );
      let emptyFields = [];

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          emptyFields.push(field.placeholder || field.getAttribute("name"));
        }
      });

      if (emptyFields.length > 0) {
        Swal.fire({
          icon: "warning",
          title: "Form Belum Lengkap!",
          html: `<ul style="text-align:left;">${emptyFields
            .map((f) => `<li>${f}</li>`)
            .join("")}</ul>`,
          confirmButtonText: "Oke",
        });
      } else {
        form.submit();
      }
    });
  }

  setupValidation('form[action=""][method="POST"]:nth-of-type(1)'); // Form Manajer
  setupValidation('form[action=""][method="POST"]:nth-of-type(2)'); // Form Peserta
});
