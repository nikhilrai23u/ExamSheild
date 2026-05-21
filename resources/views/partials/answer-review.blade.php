@if($session->answers)
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Question Review</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($session->answers as $index => $answer)
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Question {{ $index + 1 }}</p>
                            <h3 class="mt-1 text-base font-bold text-slate-900">{{ $answer['question_text'] }}</h3>
                        </div>
                        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $answer['is_correct'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $answer['is_correct'] ? 'Correct' : 'Incorrect' }}
                        </span>
                    </div>

                    @if(! empty($answer['options']))
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($answer['options'] as $optionIndex => $option)
                                @php
                                    $isStudentAnswer = (string) $answer['student_answer'] === (string) $optionIndex;
                                    $isCorrectAnswer = (string) $answer['correct_answer'] === (string) $optionIndex;
                                @endphp
                                <div class="rounded-lg border p-3 text-sm {{ $isCorrectAnswer ? 'border-green-200 bg-green-50 text-green-800 font-semibold' : ($isStudentAnswer ? 'border-red-200 bg-red-50 text-red-800 font-semibold' : 'border-slate-200 bg-slate-50 text-slate-600') }}">
                                    <div class="flex items-center justify-between gap-3">
                                        <span>{{ $option }}</span>
                                        @if($isCorrectAnswer)
                                            <span class="text-xs font-bold">Right answer</span>
                                        @elseif($isStudentAnswer)
                                            <span class="text-xs font-bold">Selected</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Student Answer</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $answer['student_answer_label'] ?? 'Not answered' }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                            <p class="text-xs font-bold text-green-700 uppercase tracking-wider">Correct Answer</p>
                            <p class="mt-1 font-semibold text-green-900">{{ $answer['correct_answer_label'] }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3">
                            <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">Points</p>
                            <p class="mt-1 font-semibold text-blue-900">{{ $answer['earned_points'] }} / {{ $answer['points'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
