import { computed, onBeforeUnmount, ref } from 'vue';

export function useTypingGame() {
    const targetText = ref('');
    const targetWpm = ref(0);
    const minAccuracy = ref(80);
    const timeLimitSeconds = ref(null);

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

    const mistakes = computed(() => wrongChars.value);

    const accuracy = computed(() => {
        if (typedText.value.length === 0) {
            return 100;
        }

        return Number(((correctChars.value / typedText.value.length) * 100).toFixed(2));
    });

    const wpm = computed(() => {
        if (!startedAt.value || elapsedSeconds.value <= 0) {
            return 0;
        }

        const minutes = elapsedSeconds.value / 60;
        return Number(((correctChars.value / 5) / minutes).toFixed(2));
    });

    const score = computed(() => {
        const rawScore = Math.round((wpm.value * 10) + (accuracy.value * 5) - (mistakes.value * 2));
        return Math.max(rawScore, 0);
    });

    const isCompleted = computed(() => {
        return status.value === 'finished' && typedText.value === targetText.value;
    });

    const stars = computed(() => {
        if (!isCompleted.value) {
            return 0;
        }

        if (wpm.value >= targetWpm.value && accuracy.value >= 95) {
            return 3;
        }

        if (wpm.value >= targetWpm.value * 0.8 && accuracy.value >= minAccuracy.value) {
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
            failed: 'Waktu habis',
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
        mistakes,
        accuracy,
        wpm,
        score,
        stars,
        isCompleted,
        inputDisabled,
        setupGame,
        resetGame,
        updateTypedText,
        getCharacterClass,
    };
}