<template>
    <div class="bible-verse-finder">
        <!-- Parse Reference Input -->
        <div v-if="meta.enableReferenceParsing" class="mb-6">
            <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-175 mb-2 block">
                Quick Add: Paste Reference
            </label>
            <div class="flex gap-2">
                <Input
                    v-model="referenceInput"
                    @keypress.enter="parseAndAddReference"
                    placeholder="e.g., Psalm 46, John 3:16-17 NKJV"
                    class="flex-1"
                />
                <Button
                    @click="parseAndAddReference"
                    variant="primary"
                    :disabled="!referenceInput.trim()"
                    text="Parse & Add"
                />
            </div>
            <div class="text-xs text-gray-500 dark:text-dark-175 mt-1">
                Examples: "Psalm 46", "Psalm 46-47", "John 3:16", "John 3:16-17 NKJV"
            </div>
        </div>

        <!-- Verses List -->
        <div class="space-y-4 mb-6">
            <Card
                v-for="(verse, index) in verses"
                :key="index"
                class="p-4"
                :class="{ 'opacity-60': verse.fetching }"
            >
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 dark:border-dark-400">
                    <h4 class="text-base font-bold text-gray-700 dark:text-gray-100">Scripture {{ index + 1 }}</h4>
                    <Button
                        @click="removeVerse(index)"
                        variant="ghost"
                        icon="trash"
                        size="sm"
                        title="Remove verse"
                    />
                </div>

                <div class="space-y-4">
                    <!-- Book Selection -->
                    <div>
                        <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-175 mb-1 block">Book</label>
                        <Select
                            :model-value="verse.book"
                            @update:model-value="updateBook(index, $event)"
                            :options="allBooks"
                            :disabled="verse.fetching"
                            placeholder="Select Book..."
                        />
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <!-- Start Chapter -->
                        <div>
                            <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-175 mb-1 block">Chapter</label>
                            <Input
                                type="number"
                                :model-value="verse.chapter"
                                @update:model-value="updateChapter(index, $event)"
                                min="1"
                                :disabled="!verse.book || verse.fetching"
                            />
                        </div>

                        <!-- End Chapter (for chapter ranges) -->
                        <div>
                            <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-175 mb-1 block">End Chapter</label>
                            <Input
                                type="number"
                                :model-value="verse.end_chapter"
                                @update:model-value="updateEndChapter(index, $event)"
                                min="1"
                                :disabled="!verse.chapter || verse.fetching"
                                placeholder="Optional"
                            />
                        </div>

                        <!-- Start Verse -->
                        <div>
                            <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-175 mb-1 block">Start Verse</label>
                            <Input
                                type="number"
                                :model-value="verse.start_verse"
                                @update:model-value="updateStartVerse(index, $event)"
                                min="1"
                                :disabled="!verse.chapter || verse.fetching || verse.end_chapter"
                                placeholder="Optional"
                            />
                        </div>

                        <!-- End Verse -->
                        <div>
                            <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-175 mb-1 block">End Verse</label>
                            <Input
                                type="number"
                                :model-value="verse.end_verse"
                                @update:model-value="updateEndVerse(index, $event)"
                                min="1"
                                :disabled="!verse.chapter || verse.fetching || verse.end_chapter"
                                placeholder="Optional"
                            />
                        </div>

                        <!-- Version -->
                        <div>
                            <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-dark-175 mb-1 block">Version</label>
                            <Select
                                :model-value="verse.version"
                                @update:model-value="verse.version = $event"
                                :options="meta.versions"
                                :disabled="verse.fetching"
                            />
                        </div>
                    </div>

                    <!-- Help text for chapter vs verse ranges -->
                    <div v-if="verse.chapter" class="text-xs text-gray-500 dark:text-dark-175">
                        <span v-if="verse.end_chapter">Chapter range: Will fetch all verses from {{ verse.book }} {{ verse.chapter }} through {{ verse.end_chapter }}</span>
                        <span v-else-if="!verse.start_verse">Whole chapter: Will fetch all verses from {{ verse.book }} {{ verse.chapter }}</span>
                        <span v-else-if="verse.end_verse">Verse range: {{ verse.book }} {{ verse.chapter }}:{{ verse.start_verse }}-{{ verse.end_verse }}</span>
                        <span v-else>Single verse: {{ verse.book }} {{ verse.chapter }}:{{ verse.start_verse }}</span>
                    </div>

                    <!-- Fetch Button -->
                    <div class="flex gap-2">
                        <Button
                            @click="fetchVerse(index)"
                            variant="primary"
                            :disabled="!canFetch(verse) || verse.fetching"
                            :loading="verse.fetching"
                            icon="arrows-counterclockwise"
                            :text="verse.fetching ? 'Fetching...' : 'Fetch Verse'"
                        />
                    </div>

                    <!-- Error Message -->
                    <Alert v-if="verse.error" variant="danger">
                        {{ verse.error }}
                    </Alert>

                    <!-- Preview -->
                    <div v-if="verse.text && !verse.error" class="mt-4 p-4 bg-gradient-to-br from-green-50 via-emerald-50 to-blue-50 dark:from-green-900/30 dark:via-emerald-900/30 dark:to-blue-900/30 border-2 border-green-400 dark:border-green-500 rounded-lg">
                        <div class="flex items-center gap-2 mb-3 pb-3 border-b-2 border-green-300 dark:border-green-500">
                            <Icon name="check-circle" class="size-5 text-green-600 dark:text-green-400" />
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ verse.reference }}</span>
                            <span class="text-sm text-gray-600 dark:text-gray-300">({{ verse.version }})</span>
                        </div>
                        <div class="text-gray-900 dark:text-gray-100 leading-relaxed mb-3 text-base font-serif italic px-3 py-2 bg-white/50 dark:bg-gray-800/50 rounded">
                            {{ verse.text }}
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 flex items-center gap-1.5">
                            <Icon name="clock" class="size-3 text-gray-400 dark:text-gray-500" />
                            Fetched: {{ formatDate(verse.fetched_at) }}
                        </div>
                    </div>
                </div>
            </Card>

            <!-- No verses message -->
            <div v-if="verses.length === 0" class="text-center py-8 text-gray-500 dark:text-dark-175 border-2 border-dashed border-gray-300 dark:border-dark-400 rounded">
                <Icon name="book-open" class="size-12 mx-auto mb-2 text-gray-400 dark:text-dark-300" />
                <p>No verses added yet</p>
                <p class="text-sm">Click "Add Verse" below to get started</p>
            </div>
        </div>

        <!-- Add/Refresh Buttons -->
        <div class="flex gap-2">
            <Button
                @click="addVerse"
                :disabled="maxVersesReached"
                icon="plus"
                text="Add Verse"
            />
            <Button
                v-if="verses.length > 0"
                @click="refreshAllVerses"
                :disabled="refreshing"
                :loading="refreshing"
                icon="arrows-counterclockwise"
                :text="refreshing ? 'Refreshing...' : 'Refresh All'"
            />
        </div>
    </div>
</template>

<script>
import { Fieldtype } from '@statamic/cms';
import {
    Alert,
    Button,
    Card,
    Icon,
    Input,
    Select,
} from '@statamic/cms/ui';

export default {
    mixins: [Fieldtype],

    components: {
        Alert,
        Button,
        Card,
        Icon,
        Input,
        Select,
    },

    data() {
        return {
            verses: [],
            referenceInput: '',
            refreshing: false,
            initializing: true,
        };
    },

    computed: {
        allBooks() {
            return this.meta.books || [];
        },

        maxVersesReached() {
            const max = this.meta.maxVerses;
            return max > 0 && this.verses.length >= max;
        },
    },

    watch: {
        verses: {
            deep: true,
            handler(newVerses) {
                // Skip emitting during initialization to avoid dirty state
                if (this.initializing) return;

                // Filter out verses without text before updating
                const validVerses = newVerses.filter(v => v.text && v.text.trim() !== '');
                this.$emit('update:value', validVerses);
            },
        },
    },

    mounted() {
        // Initialize with existing data
        if (this.value && Array.isArray(this.value)) {
            this.verses = this.value.map(verse => ({
                ...verse,
                fetching: false,
                error: null,
            }));
        }

        // Clear initializing flag after mount to allow future updates
        this.$nextTick(() => {
            this.initializing = false;
        });
    },

    methods: {
        addVerse() {
            if (this.maxVersesReached) return;

            this.verses.push({
                book: '',
                chapter: null,
                end_chapter: null,
                start_verse: null,
                end_verse: null,
                version: this.meta.defaultVersion || 'NKJV',
                text: '',
                reference: '',
                fetched_at: null,
                api_source: null,
                fetching: false,
                error: null,
            });
        },

        removeVerse(index) {
            this.verses.splice(index, 1);
        },

        canFetch(verse) {
            return verse.book && verse.chapter > 0 && verse.version;
        },

        async fetchVerse(index) {
            const verse = this.verses[index];

            if (!this.canFetch(verse)) {
                return;
            }

            verse.fetching = true;
            verse.error = null;

            try {
                const response = await this.$axios.post('/cp/bible-verses/fetch', {
                    book: verse.book,
                    chapter: verse.chapter,
                    end_chapter: verse.end_chapter || null,
                    start_verse: verse.start_verse || null,
                    end_verse: verse.end_verse || null,
                    version: verse.version,
                });

                if (response.data.success) {
                    Object.assign(verse, response.data.verse);
                    verse.error = null;
                } else {
                    verse.error = response.data.error || 'Failed to fetch verse';
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.error) {
                    verse.error = error.response.data.error;
                } else {
                    verse.error = 'Network error: ' + error.message;
                }
            } finally {
                verse.fetching = false;
            }
        },

        async refreshAllVerses() {
            this.refreshing = true;

            for (let i = 0; i < this.verses.length; i++) {
                if (this.canFetch(this.verses[i])) {
                    await this.fetchVerse(i);
                }
            }

            this.refreshing = false;
        },

        async parseAndAddReference() {
            if (!this.referenceInput.trim()) return;

            try {
                const response = await this.$axios.post('/cp/bible-verses/parse', {
                    reference: this.referenceInput,
                });

                if (response.data.success) {
                    const parsed = response.data.parsed;

                    this.verses.push({
                        book: parsed.book,
                        chapter: parsed.chapter,
                        end_chapter: parsed.end_chapter || null,
                        start_verse: parsed.start_verse || null,
                        end_verse: parsed.end_verse || null,
                        version: parsed.version,
                        text: '',
                        reference: '',
                        fetched_at: null,
                        api_source: null,
                        fetching: false,
                        error: null,
                    });

                    await this.fetchVerse(this.verses.length - 1);
                    this.referenceInput = '';
                } else {
                    alert(response.data.error || 'Failed to parse reference');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.error) {
                    alert(error.response.data.error);
                } else {
                    alert('Error parsing reference: ' + error.message);
                }
            }
        },

        toNumber(value) {
            if (value === '' || value === null || value === undefined) return null;
            const num = parseInt(value, 10);
            return isNaN(num) ? null : num;
        },

        updateBook(index, value) {
            const verse = this.verses[index];
            verse.book = value;
            verse.chapter = null;
            verse.end_chapter = null;
            verse.start_verse = null;
            verse.end_verse = null;
            verse.text = '';
            verse.error = null;
        },

        updateChapter(index, value) {
            const verse = this.verses[index];
            verse.chapter = this.toNumber(value);
            verse.end_chapter = null;
            verse.start_verse = null;
            verse.end_verse = null;
            verse.text = '';
            verse.error = null;
        },

        updateEndChapter(index, value) {
            const verse = this.verses[index];
            verse.end_chapter = this.toNumber(value);
            if (verse.end_chapter) {
                verse.start_verse = null;
                verse.end_verse = null;
            }
            verse.text = '';
            verse.error = null;
        },

        updateStartVerse(index, value) {
            const verse = this.verses[index];
            verse.start_verse = this.toNumber(value);
        },

        updateEndVerse(index, value) {
            const verse = this.verses[index];
            verse.end_verse = this.toNumber(value);
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleString();
        },
    },
};
</script>
