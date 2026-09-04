@extends('layouts.lms')
@section('content')
<div style="width: 100%; max-width: 1100px; margin: 0 auto; padding: 2rem; box-sizing: border-box;">
    
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
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                
                <!-- Left Panel: Question Navigator -->
                <div style="width: 250px; flex-shrink: 0;">
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0; position: sticky; top: 1rem;">
                        <h4 style="margin-top: 0; margin-bottom: 1rem; font-size: 1rem;">Navigasi Soal</h4>
                        <div id="question-nav" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.5rem;">
                            @foreach($quiz->questions as $index => $q)
                                <div class="nav-box" data-index="{{ $index }}" onclick="goToQuestion({{ $index }})" style="aspect-ratio: 1; display: flex; align-items: center; justify-content: center; background: #fef08a; border: 1px solid #fde047; border-radius: 4px; font-weight: bold; cursor: pointer; user-select: none;">
                                    {{ $index + 1 }}
                                </div>
                            @endforeach
                        </div>
                        <div style="margin-top: 1.5rem; border-top: 1px solid #e2e8f0; padding-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 16px; height: 16px; background: #10b981; border-radius: 2px;"></div> Sudah Diisi
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 16px; height: 16px; background: #fef08a; border-radius: 2px;"></div> Belum Diisi / Dilewati
                            </div>
                        </div>
                        <button type="submit" class="btn" style="width: 100%; margin-top: 1.5rem; padding: 0.75rem; font-size: 1rem; background: var(--success, #10b981); color: white; border: none; border-radius: 6px; cursor:pointer;" class="swal-confirm-btn" data-swal-msg="Apakah Anda yakin ingin submit kuis ini? Pastikan semua soal telah dijawab semampu Anda.">
                            Submit Kuis
                        </button>
                    </div>
                </div>

                <!-- Right Panel: Questions Content -->
                <div style="flex-grow: 1; min-width: 0;">
                    @foreach($quiz->questions as $index => $q)
                        <div class="question-pane" id="question-pane-{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }}; min-height: 300px;">
                            <div style="margin-bottom: 2rem; padding-bottom: 1rem;">
                                <p style="font-size: 1.1rem; margin-bottom: 1.5rem;"><strong>Soal {{ $index + 1 }}</strong> <span style="color:var(--text-muted); font-size:0.9rem; float:right;">(Poin: {{ $q->points }})</span></p>
                                <div style="font-size: 1.05rem; margin-bottom: 1.5rem;">{!! nl2br(e($q->question_text)) !!}</div>
                                @if($q->question_image)
                                    <div style="margin-bottom: 1.5rem;">
                                        <img src="{{ asset('storage/' . $q->question_image) }}" style="max-height:300px; max-width:100%; border-radius:8px; border:1px solid #e2e8f0;">
                                    </div>
                                @endif
                                
                                @if($q->type == 'multiple_choice')
                                    @php $options = json_decode($q->options, true); @endphp
                                    @if(is_array($options))
                                        @foreach($options as $key => $opt)
                                            <label style="display: flex; align-items:flex-start; margin-bottom: 0.75rem; cursor:pointer; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                                <input type="radio" name="q_{{ $q->id }}" value="{{ $key }}" class="answer-input" data-index="{{ $index }}" onchange="markAnswered({{ $index }})" style="accent-color: var(--primary); margin-right: 1rem; margin-top:0.25rem;"> 
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
                                    <textarea name="q_{{ $q->id }}" class="answer-input" data-index="{{ $index }}" oninput="markAnswered({{ $index }})" rows="6" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem;" placeholder="Ketik jawaban essay Anda di sini..."></textarea>
                                @endif
                            </div>
                            
                            <!-- Navigation Buttons -->
                            <div style="display: flex; justify-content: space-between; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                                @if($index > 0)
                                    <button type="button" class="btn" style="padding: 0.75rem 1.5rem; background: #e2e8f0; color: #475569; border: none; border-radius: 6px; cursor:pointer;" onclick="goToQuestion({{ $index - 1 }})">
                                        &laquo; Previous
                                    </button>
                                @else
                                    <div></div> <!-- Spacer -->
                                @endif

                                <div style="display: flex; gap: 1rem;">
                                    @if($index < count($quiz->questions) - 1)
                                        <button type="button" class="btn" style="padding: 0.75rem 1.5rem; background: #fef08a; color: #854d0e; border: none; border-radius: 6px; cursor:pointer;" onclick="goToQuestion({{ $index + 1 }})">
                                            Skip &raquo;
                                        </button>
                                        <button type="button" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border: none; border-radius: 6px; cursor:pointer;" onclick="goToQuestion({{ $index + 1 }})">
                                            Next &raquo;
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success" style="padding: 0.75rem 1.5rem; border: none; border-radius: 6px; cursor:pointer;" onclick="document.getElementById('quiz-form').submit()">
                                            Submit Jawaban Akhir
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
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
    let currentQuestionIndex = 0;
    const totalQuestions = {{ count($quiz->questions) }};

    function goToQuestion(index) {
        if (index < 0 || index >= totalQuestions) return;
        
        // Hide current
        document.getElementById('question-pane-' + currentQuestionIndex).style.display = 'none';
        
        // Remove active highlight from nav
        const oldNav = document.querySelector('.nav-box[data-index="' + currentQuestionIndex + '"]');
        if(oldNav) oldNav.style.boxShadow = 'none';

        // Update index
        currentQuestionIndex = index;
        
        // Show new
        document.getElementById('question-pane-' + currentQuestionIndex).style.display = 'block';
        
        // Highlight nav
        const newNav = document.querySelector('.nav-box[data-index="' + currentQuestionIndex + '"]');
        if(newNav) newNav.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.5)';
    }

    function markAnswered(index) {
        const pane = document.getElementById('question-pane-' + index);
        const inputs = pane.querySelectorAll('.answer-input');
        let isAnswered = false;
        
        inputs.forEach(input => {
            if (input.type === 'radio' && input.checked) {
                isAnswered = true;
            } else if (input.tagName.toLowerCase() === 'textarea' && input.value.trim() !== '') {
                isAnswered = true;
            }
        });

        const navBox = document.querySelector('.nav-box[data-index="' + index + '"]');
        if (isAnswered) {
            navBox.style.background = '#10b981'; // green
            navBox.style.borderColor = '#059669';
            navBox.style.color = 'white';
        } else {
            navBox.style.background = '#fef08a'; // yellow
            navBox.style.borderColor = '#fde047';
            navBox.style.color = 'black';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const startNav = document.querySelector('.nav-box[data-index="0"]');
        if(startNav) startNav.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.5)';
    });

    let warningCount = 0;
    const maxWarnings = 3;
    let quizActive = false;
    let isSubmitting = false;
    let lastViolationTime = 0; // Cooldown tracker
    
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
        
        // Cooldown 3 detik untuk mencegah deteksi ganda dari satu aksi (misal pindah tab memicu blur + visibilitychange sekaligus)
        const now = Date.now();
        if (now - lastViolationTime < 3000) return;
        lastViolationTime = now;
        
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
