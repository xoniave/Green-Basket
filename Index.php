
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Green Basket</title>
<style>
body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #008751;
    color: white;
    font-family: 'Segoe UI', sans-serif;
    overflow: hidden;
}

h1 {
    font-size: 4rem;
    letter-spacing: 0.5rem;
    text-transform: uppercase;
    display: flex;
    justify-content: center;
}

h1 span {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 1s forwards;
}

h1 span:nth-child(1) { animation-delay: 0s; }
h1 span:nth-child(2) { animation-delay: 0.1s; }
h1 span:nth-child(3) { animation-delay: 0.2s; }
h1 span:nth-child(4) { animation-delay: 0.3s; }
h1 span:nth-child(5) { animation-delay: 0.4s; }
h1 span:nth-child(6) { animation-delay: 0.5s; }
h1 span:nth-child(7) { animation-delay: 0.6s; }
h1 span:nth-child(8) { animation-delay: 0.7s; }
h1 span:nth-child(9) { animation-delay: 0.8s; }
h1 span:nth-child(10){ animation-delay: 0.9s; }
h1 span:nth-child(11){ animation-delay: 1s; }

@keyframes fadeUp {
    0% { opacity: 0; transform: translateY(20px); text-shadow: none; }
    50% { opacity: 1; transform: translateY(-5px); text-shadow: 0 0 10px white; }
    100% { opacity: 1; transform: translateY(0); text-shadow: 0 0 20px white; }
}
</style>
</head>
<body>

<h1>
    <span>G</span><span>R</span><span>E</span><span>E</span><span>N</span>
    <span> </span>
    <span>B</span><span>A</span><span>S</span><span>K</span><span>E</span><span>T</span>
</h1>

<script>
setTimeout(() => {
    window.location.href = "login.php"; // redirect setelah animasi
}, 2500); // 3 detik
</script>

</body>
</html>
