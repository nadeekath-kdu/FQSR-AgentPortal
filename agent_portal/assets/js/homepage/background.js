$(document).ready(function () {
  /* const images = [
    '../assets/img/kdu/web/new1.webp',
    '../assets/img/kdu/web/0E9A0849.webp',
    '../assets/img/kdu/web/0E9A0850.webp',
    '../assets/img/kdu/web/0E9A0853.webp',
    '../assets/img/kdu/web/0E9A2158.webp',
    '../assets/img/kdu/web/5.webp',
    '../assets/img/kdu/web/9.webp',
    '../assets/img/kdu/web/FDSS.webp',
    '../assets/img/kdu/web/fdss2.webp',
    '../assets/img/kdu/web/KDUM1701.webp',
    '../assets/img/kdu/web/KDUM1705.webp',
    '../assets/img/kdu/web/KDUM6840.webp',
    '../assets/img/kdu/web/LDE_6066.webp',
    '../assets/img/kdu/web/DSC_4052.webp',
    '../assets/img/kdu/web/DSC_5205.webp',
    '../assets/img/kdu/web/DSC_6310.webp',
    '../assets/img/kdu/web/DSC_6320.webp',
    '../assets/img/kdu/web/DSC_6341.webp',
    '../assets/img/kdu/web/DSC_9030.webp',
    '../assets/img/kdu/web/DSC_9032.webp',
    '../assets/img/kdu/web/DSC_9034.webp'
]; */
  const images = [
    '../assets/img/kdu/web/fs.webp'
  ];

  function changeBackgroundImage() {
    const randomIndex = Math.floor(Math.random() * images.length);
    const imageUrl = images[randomIndex];
    $('#backgroundImage').css('background-image', 'url(' + imageUrl + ')');
  }

  changeBackgroundImage(); // Initial call to set the background image

  setInterval(changeBackgroundImage, 5000);
});
