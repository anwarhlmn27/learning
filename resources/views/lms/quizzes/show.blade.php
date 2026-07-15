@extends('layouts.lms')
@section('content')
<div class="container" style="max-width: 1000px; margin: 0 auto; padding: 2rem 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Kelola Kuis: {{ $quiz->title }}</h2>
        <a href="{{ route('classes.show', $quiz->class_room_id) }}" class="btn btn-outline">Kembali ke Kelas</a>
    </div>



    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div>
            <!-- Bank Soal -->
            <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0;">Daftar Soal</h3>
                    <button onclick="document.getElementById('modal-add-question').style.display='block'" class="btn" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">+ Tambah Soal</button>
                </div>
                <div style="padding: 1.5rem;">
                    @forelse($quiz->questions as $index => $q)
                        <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9;">
                            <div style="display: flex; justify-content: space-between;">
                                <strong>{{ $index + 1 }}. {{ $q->question_text }}</strong>
                                <form action="{{ route('quizzes.destroy_question', $q) }}" method="POST" class="swal-confirm-form" data-swal-msg="Yakin hapus soal ini?">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:none; border:none; color:red; cursor:pointer;">Hapus</button>
                                </form>
                            </div>
                            @if($q->question_image)
                                <div style="margin-top:0.5rem;">
                                    <img src="{{ asset('storage/' . $q->question_image) }}" style="max-height:200px; border-radius:4px; border:1px solid #e2e8f0;">
                                </div>
                            @endif
                            <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #64748b;">
                                Tipe: {{ $q->type == 'multiple_choice' ? 'Pilihan Ganda' : 'Essay' }} | Poin: {{ $q->points }}
                            </div>
                            @if($q->type == 'multiple_choice')
                                <ul style="margin-top: 0.5rem; padding-left: 1.5rem; font-size: 0.9rem;">
                                    @foreach(json_decode($q->options, true) as $key => $opt)
                                        <li style="{{ (string)$key === $q->correct_option ? 'font-weight:bold; color:green;' : '' }}">
                                            @if(is_string($opt))
                                                {{ $opt }}
                                            @else
                                                @if(!empty($opt['text']))
                                                    {{ $opt['text'] }}
                                                @endif
                                                @if(!empty($opt['image']))
                                                    <br><img src="{{ asset('storage/' . $opt['image']) }}" style="max-height:100px; border:1px solid #e2e8f0; border-radius:4px; margin-top:0.25rem;">
                                                @endif
                                            @endif
                                            {{ (string)$key === $q->correct_option ? ' (Jawaban Benar)' : '' }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @empty
                        <p style="text-align: center; color: var(--text-muted);">Belum ada soal ditambahkan.</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div>
            <!-- Hasil / Submissions -->
            <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #f8fafc;">
                    <h3 style="margin: 0;">Submission Mahasiswa</h3>
                </div>
                <div style="padding: 1rem;">
                    @forelse($quiz->attempts as $attempt)
                        <div style="padding: 1rem; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 0.5rem;">
                            <strong>{{ $attempt->user->name }}</strong>
                            <div style="font-size: 0.85rem; margin-top: 0.25rem;">
                                Status: {{ $attempt->is_submitted ? 'Submitted' : 'On Progress' }} <br>
                                Score: {{ $attempt->score ?? 'Menunggu Grading Essay' }}
                            </div>
                            @if($attempt->is_submitted && $quiz->questions->where('type', 'essay')->count() > 0)
                                <a href="{{ route('quizzes.grade_form', [$quiz, $attempt]) }}" class="btn btn-outline" style="display: block; text-align: center; margin-top: 0.5rem; padding: 0.25rem;">Grade Essay</a>
                            @endif
                        </div>
                    @empty
                        <p style="text-align: center; color: var(--text-muted); font-size:0.9rem;">Belum ada mahasiswa yang mensubmit.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Question -->
<div id="modal-add-question" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; overflow-y:auto;">
    <div style="background:white; max-width:600px; margin: 50px auto; border-radius:8px; padding:1.5rem;">
        <h3>Tambah Soal</h3>
        <form action="{{ route('quizzes.store_question', $quiz) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label>Tipe Soal</label>
                <select name="type" id="q_type" style="width:100%; padding:0.5rem;" onchange="toggleType(this.value)">
                    <option value="multiple_choice">Pilihan Ganda</option>
                    <option value="essay">Essay</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label>Pertanyaan</label>
                <textarea name="question_text" rows="3" required style="width:100%; padding:0.5rem;"></textarea>
            </div>
            <div style="margin-bottom: 1rem;">
                <label>Gambar Soal (Opsional)</label>
                <input type="file" name="question_image" accept="image/*" style="width:100%; padding:0.5rem;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label>Bobot Nilai (Poin)</label>
                <input type="number" name="points" value="10" required style="width:100%; padding:0.5rem;">
            </div>
            
            <div id="mc_options" style="margin-bottom: 1rem; background: #f8fafc; padding: 1rem; border-radius: 4px;">
                <label style="display:block; margin-bottom:0.5rem;">Pilihan Ganda (Pilih radio button untuk Kunci Jawaban Benar)</label>
                @for($i=0; $i<4; $i++)
                    <div style="display:flex; gap:10px; margin-bottom:10px; align-items:center;">
                        <input type="radio" name="correct_option" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }} required title="Pilih sebagai jawaban benar">
                        <div style="flex:1;">
                            <input type="text" name="options[{{ $i }}][text]" placeholder="Teks Opsi {{ $i + 1 }}" style="width:100%; padding:0.5rem; margin-bottom:0.25rem;">
                            <input type="file" name="options[{{ $i }}][image]" accept="image/*" style="width:100%; font-size:0.85rem;">
                        </div>
                    </div>
                @endfor
            </div>

            <div style="text-align: right; margin-top:1.5rem;">
                <button type="button" onclick="document.getElementById('modal-add-question').style.display='none'" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn">Simpan Soal</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleType(val) {
    document.getElementById('mc_options').style.display = (val === 'multiple_choice') ? 'block' : 'none';
}
</script>
@endsection
