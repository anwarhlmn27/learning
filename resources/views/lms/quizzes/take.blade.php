@extends('layouts.lms')
@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto; padding: 2rem 1rem;">
    
    <!-- Intro Screen -->
    <div id="quiz-intro">
        <h2>{{ $quiz->title }}</h2>
        <p>{{ $quiz->description }}</p>
        <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <strong>Durasi:</strong> {{ $quiz->duration }} Menit <br>
            <strong>Bobot Penilaian:</strong> {{ $quiz->rpsAssessment ? $quiz->rpsAssessment->assessment_name . ' (' . $quiz->rpsAssessment->weight . '%)' : 'Tidak masuk penilaian OBE' }}
        </div>
        
        <div style="background: #fffbeb; color: #b45309; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #fde68a;">
            <h4 style="margin-top:0; margin-bottom:0.5rem; color: #92400e;">⚠️ Perhatian: Sistem Pengawasan Ujian (Proctoring)</h4>
            <p style="margin:0; font-size: 0.9rem;">
                Kuis ini membutuhkan mode <strong>Fullscreen</strong>. Selama pengerjaan, dilarang keras meninggalkan halaman ujian (seperti membuka tab baru, pindah aplikasi, atau keluar dari fullscreen). <br><br>
                Sistem akan mencatat pelanggaran Anda. <strong>Maksimal pelanggaran adalah 3 kali</strong>. Pada pelanggaran ke-3, kuis akan otomatis diselesaikan dan disubmit.
            </p>
        </div>

        <button type="button" class="btn" id="start-quiz-btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; background: var(--primary); color: white; border: none; border-radius: 6px; cursor:pointer;">
            Mulai Kuis Sekarang
        </button>
    </div>

    <!-- Quiz Content -->
    <div id="quiz-container" style="display: none; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
            <h3 style="margin:0;">{{ $quiz->title }}</h3>
            <div id="warning-badge" style="background: #fef2f2; color: #991b1b; padding: 0.35rem 0.75rem; border-radius: 99px; font-size: 0.85rem; font-weight: bold; border: 1px solid #fecaca;">
                Pelanggaran: 0 / 3
            </div>
        </div>

        <form id="quiz-form" action="{{ route('classes.submit_quiz', [$class, $quiz]) }}" method="POST">
            @csrf
            @foreach($quiz->questions as $index => $q)
                <div style="margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
                    <p><strong>{{ $index + 1 }}.</strong> {{ $q->question_text }} <span style="color:var(--text-muted); font-size:0.8rem;">(Poin: {{ $q->points }})</span></p>
                    @if($q->question_image)
                        <div style="margin-bottom: 1rem;">
                            <img src="{{ asset('storage/' . $q->question_image) }}" style="max-height:300px; max-width:100%; border-radius:8px; border:1px solid #e2e8f0;">
                        </div>
                    @endif
                    
                    @if($q->type == 'multiple_choice')
                        @php $options = json_decode($q->options, true); @endphp
                        @if(is_array($options))
                            @foreach($options as $key => $opt)
                                <label style="display: flex; align-items:flex-start; margin-bottom: 0.75rem; cursor:pointer; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <input type="radio" name="q_{{ $q->id }}" value="{{ $key }}" required style="accent-color: var(--primary); margin-right: 1rem; margin-top:0.25rem;"> 
                                    <div style="flex:1;">
                                        @if(!empty($opt['text']))
                                            <div style="margin-bottom: {{ !empty($opt['image']) ? '0.5rem' : '0' }}; font-size:1.05rem;">{{ $opt['text'] }}</div>
                                        @endif
                                        @if(!empty($opt['image']))
                                            <img src="{{ asset('storage/' . $opt['image']) }}" style="max-height:150px; border:1px solid #e2e8f0; border-radius:4px;">
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        @endif
                    @else
                        <textarea name="q_{{ $q->id }}" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px;" required placeholder="Ketik jawaban essay Anda di sini..."></textarea>
                    @endif
                </div>
            @endforeach

            <button type="submit" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; background: var(--success, #10b981); color: white; border: none; border-radius: 6px; cursor:pointer;">
                Submit Jawaban Akhir
            </button>
        </form>
    </div>

    <!-- Resume Screen -->
    <div id="quiz-resume" style="display: none; background: white; padding: 3rem; border-radius: 8px; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h3 style="color: #991b1b; margin-top: 0; font-size: 1.5rem;">Ujian Terhenti!</h3>
        <p style="margin-bottom: 2rem;">Anda keluar dari mode fullscreen. Layar harus dalam keadaan penuh selama ujian berlangsung. Silakan klik tombol di bawah untuk kembali melanjutkan ujian.</p>
        <button type="button" class="btn" id="resume-quiz-btn" style="padding: 1rem 2rem; font-size: 1.1rem; background: var(--primary); color: white; border: none; border-radius: 6px; cursor:pointer;">
            Lanjutkan Ujian (Fullscreen)
        </button>
    </div>
</div>

<script>
    let warningCount = 0;
    const maxWarnings = 3;
    let quizActive = false;
    let isSubmitting = false;
    
    document.getElementById('start-quiz-btn').addEventListener('click', function() {
        const docEl = document.documentElement;
        const requestFS = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullscreen || docEl.msRequestFullscreen;
        
        if (requestFS) {
            requestFS.call(docEl).then(() => {
                startQuiz();
            }).catch(err => {
                alert('Browser menolak mode fullscreen. Silakan berikan izin atau gunakan mode landscape/layar penuh.');
                startQuiz(); // fallback
            });
        } else {
            startQuiz(); // fallback if unsupported
        }
    });

    document.getElementById('resume-quiz-btn').addEventListener('click', function() {
        const docEl = document.documentElement;
        const requestFS = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullscreen || docEl.msRequestFullscreen;
        
        if (requestFS) {
            requestFS.call(docEl).then(() => {
                document.getElementById('quiz-resume').style.display = 'none';
                document.getElementById('quiz-container').style.display = 'block';
            }).catch(err => {
                if(typeof lmsToast === 'function') lmsToast('error', 'Gagal masuk fullscreen. Silakan coba lagi.');
            });
        }
    });

    function startQuiz() {
        document.getElementById('quiz-intro').style.display = 'none';
        document.getElementById('quiz-container').style.display = 'block';
        document.body.classList.add('quiz-mode-active');
        quizActive = true;
    }

    function updateWarningUI() {
        const badge = document.getElementById('warning-badge');
        badge.innerText = `Pelanggaran: ${warningCount} / ${maxWarnings}`;
        
        if(warningCount > 0) {
            badge.style.background = '#fef2f2'; // light red
            badge.style.color = '#991b1b';
            badge.style.borderColor = '#fecaca';
        }
        if(warningCount >= 2) {
            badge.style.background = '#991b1b'; // dark red
            badge.style.color = 'white';
        }
    }

    function handleViolation() {
        if (!quizActive || isSubmitting) return;
        
        warningCount++;
        updateWarningUI();
        
        if (warningCount >= maxWarnings) {
            isSubmitting = true;
            quizActive = false;
            if(typeof lmsToast === 'function') {
                lmsToast('error', `Batas pelanggaran tercapai (${maxWarnings}/${maxWarnings}). Kuis disubmit otomatis!`, 5000);
            } else {
                alert(`Batas pelanggaran tercapai. Kuis disubmit otomatis!`);
            }
            
            // Allow toast to show briefly before submitting
            setTimeout(() => {
                document.getElementById('quiz-form').submit();
            }, 1500);
            
        } else {
            if(typeof lmsToast === 'function') {
                lmsToast('warning', `Peringatan ${warningCount}/${maxWarnings}! Jangan tinggalkan layar kuis.`, 4000);
            } else {
                alert(`Peringatan ${warningCount}/${maxWarnings}! Jangan tinggalkan layar kuis.`);
            }
        }
    }

    // 1. Detect switching tabs or minimizing window
    document.addEventListener("visibilitychange", function() {
        if (document.visibilityState === 'hidden') {
            handleViolation();
        }
    });

    // 2. Detect losing focus (alt-tabbing, clicking outside browser)
    window.addEventListener("blur", function() {
        handleViolation();
    });

    // 3. Detect exiting fullscreen mode
    const exitEvents = ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'];
    exitEvents.forEach(evt => {
        document.addEventListener(evt, function() {
            const isFS = document.fullscreenElement || document.webkitIsFullScreen || document.mozFullScreen || document.msFullscreenElement;
            if (quizActive && !isFS && !isSubmitting) {
                handleViolation();
                if (warningCount < maxWarnings) {
                    // Pause quiz and ask for user interaction to re-enter fullscreen
                    document.getElementById('quiz-container').style.display = 'none';
                    document.getElementById('quiz-resume').style.display = 'block';
                }
            }
        });
    });

    // Handle form manual submit
    document.getElementById('quiz-form').addEventListener('submit', function() {
        isSubmitting = true;
        quizActive = false;
    });
</script>
@endsection
