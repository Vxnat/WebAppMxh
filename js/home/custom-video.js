document.querySelectorAll('.container__video').forEach((container) => {
  const mainVideo = container.querySelector('video'),
    videoTimeline = container.querySelector('.video-timeline'),
    progressBar = container.querySelector('.progress-bar'),
    volumeBtn = container.querySelector('.volume i'),
    volumeSlider = container.querySelector('.left input'),
    currentVidTime = container.querySelector('.current-time'),
    videoDuration = container.querySelector('.video-duration'),
    skipBackward = container.querySelector('.skip-backward i'),
    skipForward = container.querySelector('.skip-forward i'),
    playPauseBtn = container.querySelector('.play-pause i'),
    speedBtn = container.querySelector('.playback-speed span'),
    speedOptions = container.querySelector('.speed-options'),
    pipBtn = container.querySelector('.pic-in-pic span'),
    fullScreenBtn = container.querySelector('.fullscreen i');

  let timer;

  // Ẩn điều khiển video sau 3 giây nếu không tương tác
  const hideControls = () => {
    if (mainVideo.paused) return;
    timer = setTimeout(() => {
      container.classList.remove('show-controls');
    }, 3000);
  };

  container.addEventListener('mousemove', () => {
    container.classList.add('show-controls');
    clearTimeout(timer);
    hideControls();
  });

  const formatTimeVideo = (time) => {
    let seconds = Math.floor(time % 60),
      minutes = Math.floor(time / 60) % 60,
      hours = Math.floor(time / 3600);
    seconds = seconds < 10 ? `0${seconds}` : seconds;
    minutes = minutes < 10 ? `0${minutes}` : minutes;
    hours = hours < 10 ? `0${hours}` : hours;
    return hours > 0 ? `${hours}:${minutes}:${seconds}` : `${minutes}:${seconds}`;
  };

  videoTimeline.addEventListener('mousemove', (e) => {
    let timelineWidth = videoTimeline.clientWidth;
    let offsetX = e.offsetX;
    let percent = Math.floor((offsetX / timelineWidth) * mainVideo.duration);
    const progressTime = videoTimeline.querySelector('span');
    offsetX = Math.max(20, Math.min(offsetX, timelineWidth - 20));
    progressTime.style.left = `${offsetX}px`;
    progressTime.innerText = formatTimeVideo(percent);
  });

  videoTimeline.addEventListener('click', (e) => {
    let timelineWidth = videoTimeline.clientWidth;
    mainVideo.currentTime = (e.offsetX / timelineWidth) * mainVideo.duration;
  });

  mainVideo.addEventListener('timeupdate', () => {
    let percent = (mainVideo.currentTime / mainVideo.duration) * 100;
    progressBar.style.width = `${percent}%`;
    currentVidTime.innerText = formatTimeVideo(mainVideo.currentTime);
  });

  mainVideo.addEventListener('loadeddata', () => {
    videoDuration.innerText = formatTimeVideo(mainVideo.duration);
  });

  const draggableProgressBar = (e) => {
    let timelineWidth = videoTimeline.clientWidth;
    progressBar.style.width = `${e.offsetX}px`;
    mainVideo.currentTime = (e.offsetX / timelineWidth) * mainVideo.duration;
    currentVidTime.innerText = formatTimeVideo(mainVideo.currentTime);
  };

  volumeBtn.addEventListener('click', () => {
    mainVideo.volume = mainVideo.volume === 0 ? 0.5 : 0;
    volumeBtn.classList.toggle('fa-volume-high', mainVideo.volume > 0);
    volumeBtn.classList.toggle('fa-volume-xmark', mainVideo.volume === 0);
    volumeSlider.value = mainVideo.volume;
  });

  volumeSlider.addEventListener('input', (e) => {
    mainVideo.volume = e.target.value;
    volumeBtn.classList.toggle('fa-volume-high', mainVideo.volume > 0);
    volumeBtn.classList.toggle('fa-volume-xmark', mainVideo.volume === 0);
  });

  speedOptions.querySelectorAll('li').forEach((option) => {
    option.addEventListener('click', () => {
      mainVideo.playbackRate = option.dataset.speed;
      speedOptions.querySelector('.active')?.classList.remove('active');
      option.classList.add('active');
    });
  });

  document.addEventListener('click', (e) => {
    if (!e.target.closest('.playback-speed')) {
      speedOptions.classList.remove('show');
    }
  });

  fullScreenBtn.addEventListener('click', () => {
    if (document.fullscreenElement) {
      document.exitFullscreen();
      fullScreenBtn.classList.replace('fa-compress', 'fa-expand');
    } else {
      container.requestFullscreen();
      fullScreenBtn.classList.replace('fa-expand', 'fa-compress');
    }
  });

  speedBtn.addEventListener('click', () => speedOptions.classList.toggle('show'));
  pipBtn.addEventListener('click', () => mainVideo.requestPictureInPicture());
  skipBackward.addEventListener('click', () => (mainVideo.currentTime -= 5));
  skipForward.addEventListener('click', () => (mainVideo.currentTime += 5));
  mainVideo.addEventListener('play', () => playPauseBtn.classList.replace('fa-play', 'fa-pause'));
  mainVideo.addEventListener('pause', () => playPauseBtn.classList.replace('fa-pause', 'fa-play'));
  playPauseBtn.addEventListener('click', () => (mainVideo.paused ? mainVideo.play() : mainVideo.pause()));
  videoTimeline.addEventListener('mousedown', () => videoTimeline.addEventListener('mousemove', draggableProgressBar));
  document.addEventListener('mouseup', () => videoTimeline.removeEventListener('mousemove', draggableProgressBar));
});
