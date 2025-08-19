<?php 
include 'templates/header.php'; 
include 'templates/sidebar.php'; 
include '../function/config.php';

// Hitung jumlah data
$total_event   = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM event"))['total'];
$total_peserta = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM peserta"))['total'];
$total_manajer = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM manajer"))['total'];
?>

<main class="main-content">
    <section class="py-5 px-4">
        <h1 class="display-5 fw-bold text-gradient" 
            style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
            Selamat Datang di Dashboard 🎉
        </h1>
        <p class="mt-3 fs-5 text-muted">Ringkasan statistik pendaftaran event.</p>
    </section>

    <div class="container mb-5">
        <div class="row g-4">
            <?php 
            $cards = [
                ['icon'=>'calendar-alt','title'=>'Total Event','color1'=>'#4facfe','color2'=>'#00f2fe','total'=>$total_event,'text'=>'primary','message'=>'Ini total event yang terdaftar!'],
                ['icon'=>'users','title'=>'Total Peserta','color1'=>'#11998e','color2'=>'#38ef7d','total'=>$total_peserta,'text'=>'success','message'=>'Jumlah peserta hingga saat ini.'],
                ['icon'=>'user-tie','title'=>'Total Manajer','color1'=>'#f7971e','color2'=>'#ffd200','total'=>$total_manajer,'text'=>'warning','message'=>'Manajer yang terdaftar di sistem.'],
            ];

            foreach($cards as $card): ?>
            <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-4 hover-card" 
                     data-message="<?= $card['message'] ?>">
                    <div class="card-body text-center p-4">
                        <div class="mb-3 icon-container">
                            <i class="fas fa-<?= $card['icon'] ?> fa-3x text-<?= $card['text'] ?>"></i>
                        </div>
                        <h5 class="card-title text-muted"><?= $card['title'] ?></h5>
                        <h2 class="fw-bold text-gradient counter gradient-text" 
                            data-target="<?= $card['total'] ?>" 
                            style="background: linear-gradient(90deg, <?= $card['color1'] ?>, <?= $card['color2'] ?>); -webkit-background-clip: text; color: transparent;">
                            0
                        </h2>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.hover-card {
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

.icon-container i {
    transition: transform 0.3s ease;
}
.hover-card:hover .icon-container i {
    transform: rotate(15deg) scale(1.2);
}

.gradient-text {
    background-size: 200% 200%;
    animation: gradientMove 3s ease infinite;
}
@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
</style>

<script>
// Counter Animation
const counters = document.querySelectorAll('.counter');
counters.forEach(counter=>{
    const target = +counter.getAttribute('data-target');
    let count=0;
    const duration=2500;
    const stepTime=20;
    const step=()=>{
        const increment=(target/duration)*stepTime;
        count+=increment;
        if(count<target){
            counter.innerText=Math.ceil(count);
            requestAnimationFrame(step);
        } else { counter.innerText=target; }
    };
    requestAnimationFrame(step);
});

// SweetAlert2 Hover
const hoverCards = document.querySelectorAll('.hover-card');
hoverCards.forEach(card=>{
    card.addEventListener('mouseenter', ()=>{
        const message = card.getAttribute('data-message');
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: message,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            background: '#1f2937',
            color: '#fff',
            iconColor: '#4facfe'
        });
    });
});
</script>

<?php include 'templates/footer.php'; ?>
