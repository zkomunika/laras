import { levelApi } from './levelApi';
import { progressApi } from './progressApi';

function getLevelNumber(value) {
    return Number(value?.level_number ?? 0);
}

function isUnlockedProgress(item) {
    return Boolean(item?.unlocked_at || item?.is_completed);
}

function sortByHighestLevelNumber(a, b) {
    return getLevelNumber(b) - getLevelNumber(a);
}

async function getFirstLevelId() {
    const levels = await levelApi.list();
    const firstLevel = [...levels]
        .sort((a, b) => getLevelNumber(a) - getLevelNumber(b))[0];

    return firstLevel?.id ?? 1;
}

export const storyResumeService = {
    async getLastUnlockedLevelId() {
        const progress = await progressApi.list();
        const highestUnlockedProgress = [...progress]
            .filter(isUnlockedProgress)
            .sort(sortByHighestLevelNumber)[0];

        if (highestUnlockedProgress?.level_id) {
            return highestUnlockedProgress.level_id;
        }

        return getFirstLevelId();
    },

    async getLastUnlockedLevelPath() {
        const levelId = await this.getLastUnlockedLevelId();
        return `/story/levels/${levelId}`;
    },
};
