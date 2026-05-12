import { computed, onBeforeUnmount, ref } from 'vue';

export function useTypingGame() {
    const targetText = ref('');
    const targetWpm = ref(0);
    const minAccuracy = ref(80);
    const timeLimitSeconds = ref(null);
    const maxMistakes = ref(null);

    const typedText = ref('');
    const status = ref('idle');
    const startedAt = ref(null);
    const finishedAt = ref(null);
    const elapsedSeconds = ref(0);

    let timer = null;

    const targetCharacters = computed(() => targetText.value.split(''));

    const correctChars = computed(() => {
        let total = 0;

        for (let i = 0; i < typedText.value.length; i++) {
            if (typedText.value[i] === targetText.value[i]) {
                total++;
            }
        }

        return total;
    });

    const wrongChars = computed(() => {
        return Math.max(typedText.value.length - correctChars.value, 0);
    });

    const missingChars = computed(() => {
        return Math.max(targetText.value.length - typedText.value.length, 0);
    });

    const mistakes = computed(() => wrongChars.value);

    const officialMistakeEstimate = computed(() => wrongChars.value + missingChars.value);

    const accuracy = computed(() => {
        if (targetText.value.length === 0) {
            return 0;
        }

        return Number(((correctChars.value / targetText.value.length) * 100).toFixed(2));
    });

    const wpm = computed(() => {
        if (!startedAt.value || elapsedSeconds.value <= 0) {
            return 0;
        }

        const minutes = elapsedSeconds.value / 60;
        return Number(((correctChars.value / 5) / minutes).toFixed(2));
    });

    const score = computed(() => {
        const rawScore = Math.round((wpm.value * 10) + (accuracy.value * 5) - (officialMistakeEstimate.value * 2));
        return Math.max(rawScore, 0);
    });

    const isCompleted = computed(() => {
        return status.value === 'finished' && typedText.value === targetText.value;
    });

    const stars = computed(() => {
        if (!isCompleted.value) {
            return 0;
        }

        if (wpm.value >= targetWpm.value * 1.15 && accuracy.value >= 95 && mistakes.value === 0) {
            return 3;
        }

        if (wpm.value >= targetWpm.value && accuracy.value >= minAccuracy.value + 5) {
            return 2;
        }

        return 1;
    });

    const remainingSeconds = computed(() => {
        if (!timeLimitSeconds.value) {
            return null;
        }

        return Math.max(0, Math.ceil(timeLimitSeconds.value - elapsedSeconds.value));
    });

    const inputDisabled = computed(() => {
        return status.value === 'finished' || status.value === 'failed';
    });

    const statusLabel = computed(() => {
        const labels = {
            idle: 'Belum mulai',
            playing: 'Bermain',
            finished: 'Selesai',
            failed: 'Gagal',
        };

        return labels[status.value] ?? 'Belum mulai';
    });

    function setupGame(level) {
        targetText.value = level?.target_text ?? '';
        targetWpm.value = Number(level?.target_wpm ?? 0);
        minAccuracy.value = Number(level?.min_accuracy ?? 80);
        timeLimitSeconds.value = level?.time_limit_seconds
            ? Number(level.time_limit_seconds)
            : null;
        maxMistakes.value = level?.max_mistakes !== null && level?.max_mistakes !== undefined
            ? Number(level.max_mistakes)
            : null;

        resetGame();
    }

    function resetGame() {
        clearTimer();

        typedText.value = '';
        status.value = 'idle';
        startedAt.value = null;
        finishedAt.value = null;
        elapsedSeconds.value = 0;
    }

    function startGame() {
        if (status.value === 'playing') {
            return;
        }

        status.value = 'playing';
        startedAt.value = Date.now();
        finishedAt.value = null;

        timer = setInterval(() => {
            updateElapsedTime();

            if (
                timeLimitSeconds.value &&
                elapsedSeconds.value >= timeLimitSeconds.value &&
                typedText.value !== targetText.value
            ) {
                finishGame('failed');
            }
        }, 200);
    }

    function updateTypedText(value) {
        if (inputDisabled.value) {
            return;
        }

        if (status.value === 'idle') {
            startGame();
        }

        const cleanedValue = String(value).replace(/\r?\n/g, ' ');
        typedText.value = cleanedValue.slice(0, targetText.value.length);

        if (maxMistakes.value !== null && mistakes.value > maxMistakes.value) {
            finishGame('failed');
            return;
        }

        if (typedText.value === targetText.value) {
            finishGame('finished');
        }
    }

    function updateElapsedTime() {
        if (!startedAt.value) {
            elapsedSeconds.value = 0;
            return;
        }

        elapsedSeconds.value = (Date.now() - startedAt.value) / 1000;
    }

    function finishGame(finalStatus = 'finished') {
        if (status.value === 'finished' || status.value === 'failed') {
            return;
        }

        finishedAt.value = Date.now();
        updateElapsedTime();
        status.value = finalStatus;
        clearTimer();
    }

    function clearTimer() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    function getCharacterClass(index) {
        if (index >= typedText.value.length) {
            return index === typedText.value.length && status.value === 'playing'
                ? 'char-current'
                : 'char-pending';
        }

        return typedText.value[index] === targetText.value[index]
            ? 'char-correct'
            : 'char-wrong';
    }

    onBeforeUnmount(() => {
        clearTimer();
    });

    return {
        targetText,
        targetCharacters,
        typedText,
        status,
        statusLabel,
        elapsedSeconds,
        remainingSeconds,
        correctChars,
        wrongChars,
        missingChars,
        mistakes,
        officialMistakeEstimate,
        accuracy,
        wpm,
        score,
        stars,
        isCompleted,
        inputDisabled,
        setupGame,
        resetGame,
        startGame,
        finishGame,
        updateTypedText,
        getCharacterClass,
    };
}
