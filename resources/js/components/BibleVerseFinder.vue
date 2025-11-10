<template>
    <div class="bible-verse-finder">
        <!-- Parse Reference Input -->
        <div v-if="meta.enableReferenceParsing" class="mb-6">
            <label class="publish-field-label mb-2">
                Quick Add: Paste Reference
            </label>
            <div class="flex gap-2">
                <input
                    type="text"
                    v-model="referenceInput"
                    @keypress.enter="parseAndAddReference"
                    placeholder="e.g., Psalm 46, John 3:16-17 NKJV"
                    class="input-text flex-1"
                />
                <button
                    @click="parseAndAddReference"
                    type="button"
                    class="btn-primary"
                    :disabled="!referenceInput.trim()"
                >
                    Parse & Add
                </button>
            </div>
            <div class="help-block mt-1">
                Examples: "Psalm 46", "Psalm 46-47", "John 3:16", "John 3:16-17 NKJV"
            </div>
        </div>

        <!-- Verses List -->
        <div class="space-y-6 mb-6">
            <div
                v-for="(verse, index) in verses"
                :key="index"
                class="bible-verse-card"
                :class="{ 'fetching': verse.fetching, 'error': verse.error }"
            >
                <div class="verse-header">
                    <div class="flex items-center justify-between w-full">
                        <h4 class="text-base font-bold text-gray-700">{{ index + 1 }}</h4>
                        <button
                            @click="removeVerse(index)"
                            type="button"
                            class="text-gray-400 hover:text-red-600 transition-colors"
                            title="Remove verse"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="verse-body">
                    <!-- Book Selection -->
                    <div class="mb-4">
                        <label class="publish-field-label">Book</label>
                        <select
                            v-model="verse.book"
                            @change="onBookChange(index)"
                            class="input-text"
                            :disabled="verse.fetching"
                        >
                            <option value="">-- Select Book --</option>
                            <optgroup label="Old Testament">
                                <option
                                    v-for="book in oldTestamentBooks"
                                    :key="book.value"
                                    :value="book.value"
                                >
                                    {{ book.label }}
                                </option>
                            </optgroup>
                            <optgroup label="New Testament">
                                <option
                                    v-for="book in newTestamentBooks"
                                    :key="book.value"
                                    :value="book.value"
                                >
                                    {{ book.label }}
                                </option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
                        <!-- Start Chapter -->
                        <div>
                            <label class="publish-field-label">Chapter</label>
                            <input
                                type="number"
                                v-model.number="verse.chapter"
                                @change="onChapterChange(index)"
                                min="1"
                                class="input-text"
                                :disabled="!verse.book || verse.fetching"
                            />
                        </div>

                        <!-- End Chapter (for chapter ranges) -->
                        <div>
                            <label class="publish-field-label">End Chapter (optional)</label>
                            <input
                                type="number"
                                v-model.number="verse.end_chapter"
                                @change="onEndChapterChange(index)"
                                min="1"
                                class="input-text"
                                :disabled="!verse.chapter || verse.fetching"
                                placeholder="For chapter ranges"
                            />
                        </div>

                        <!-- Start Verse -->
                        <div>
                            <label class="publish-field-label">Start Verse (optional)</label>
                            <input
                                type="number"
                                v-model.number="verse.start_verse"
                                min="1"
                                class="input-text"
                                :disabled="!verse.chapter || verse.fetching || verse.end_chapter"
                                placeholder="Leave empty for whole chapter"
                            />
                        </div>

                        <!-- End Verse -->
                        <div>
                            <label class="publish-field-label">End Verse (optional)</label>
                            <input
                                type="number"
                                v-model.number="verse.end_verse"
                                min="1"
                                class="input-text"
                                :disabled="!verse.chapter || verse.fetching || verse.end_chapter"
                                placeholder="For verse ranges"
                            />
                        </div>

                        <!-- Version -->
                        <div>
                            <label class="publish-field-label">Version</label>
                            <select
                                v-model="verse.version"
                                class="input-text"
                                :disabled="verse.fetching"
                            >
                                <option
                                    v-for="version in meta.versions"
                                    :key="version.value"
                                    :value="version.value"
                                >
                                    {{ version.label }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Help text for chapter vs verse ranges -->
                    <div v-if="verse.chapter" class="help-block mb-3">
                        <span v-if="verse.end_chapter">Chapter range: Will fetch all verses from {{ verse.book }} {{ verse.chapter }} through {{ verse.end_chapter }}</span>
                        <span v-else-if="!verse.start_verse">Whole chapter: Will fetch all verses from {{ verse.book }} {{ verse.chapter }}</span>
                        <span v-else-if="verse.end_verse">Verse range: {{ verse.book }} {{ verse.chapter }}:{{ verse.start_verse }}-{{ verse.end_verse }}</span>
                        <span v-else>Single verse: {{ verse.book }} {{ verse.chapter }}:{{ verse.start_verse }}</span>
                    </div>

                    <!-- Fetch Button -->
                    <div class="mt-3 flex gap-2">
                        <button
                            @click="fetchVerse(index)"
                            type="button"
                            class="btn-primary"
                            :disabled="!canFetch(verse) || verse.fetching"
                        >
                            <span v-if="verse.fetching" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Fetching...
                            </span>
                            <span v-else>
                                <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                </svg>
                                Fetch Verse
                            </span>
                        </button>
                    </div>

                    <!-- Error Message -->
                    <div v-if="verse.error" class="error-message">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ verse.error }}
                    </div>

                    <!-- Preview -->
                    <div v-if="verse.text && !verse.error" class="verse-preview">
                        <div class="preview-header">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-semibold text-gray-900">{{ verse.reference }}</span>
                                <span class="text-sm text-gray-600">({{ verse.version }})</span>
                            </div>
                        </div>
                        <div class="preview-text">
                            {{ verse.text }}
                        </div>
                        <div class="preview-meta">
                            <svg class="w-3 h-3 text-gray-400 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            Fetched: {{ formatDate(verse.fetched_at) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- No verses message -->
            <div v-if="verses.length === 0" class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-300 rounded">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
                <p>No verses added yet</p>
                <p class="text-sm">Click "Add Verse" below to get started</p>
            </div>
        </div>

        <!-- Add/Refresh Buttons -->
        <div class="flex gap-2">
            <button
                @click="addVerse"
                type="button"
                class="btn"
                :disabled="maxVersesReached"
            >
                <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add Verse
            </button>
            <button
                v-if="verses.length > 0"
                @click="refreshAllVerses"
                type="button"
                class="btn"
                :disabled="refreshing"
            >
                <svg class="w-4 h-4 inline-block mr-1" :class="{ 'animate-spin': refreshing }" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                </svg>
                {{ refreshing ? 'Refreshing...' : 'Refresh All' }}
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    mixins: [Fieldtype],

    data() {
        return {
            verses: [],
            referenceInput: '',
            refreshing: false,
        };
    },

    computed: {
        oldTestamentBooks() {
            return (this.meta.books || []).filter(book => book.testament === 'ot');
        },

        newTestamentBooks() {
            return (this.meta.books || []).filter(book => book.testament === 'nt');
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
                // Filter out verses without text before updating
                const validVerses = newVerses.filter(v => v.text && v.text.trim() !== '');
                this.update(validVerses);
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
            // Can fetch if we have book, chapter, and version
            // Chapter ranges (end_chapter set) don't need verse numbers
            // Whole chapters (no start_verse) are also valid
            // Specific verses need start_verse
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
                const response = await axios.post('/cp/bible-verses/fetch', {
                    book: verse.book,
                    chapter: verse.chapter,
                    end_chapter: verse.end_chapter || null,
                    start_verse: verse.start_verse || null,
                    end_verse: verse.end_verse || null,
                    version: verse.version,
                });

                if (response.data.success) {
                    // Update the verse with fetched data
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
                const response = await axios.post('/cp/bible-verses/parse', {
                    reference: this.referenceInput,
                });

                if (response.data.success) {
                    const parsed = response.data.parsed;

                    // Add new verse with parsed data
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

                    // Auto-fetch the verse
                    await this.fetchVerse(this.verses.length - 1);

                    // Clear input
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

        onBookChange(index) {
            // Reset chapter and verses when book changes
            const verse = this.verses[index];
            verse.chapter = null;
            verse.end_chapter = null;
            verse.start_verse = null;
            verse.end_verse = null;
            verse.text = '';
            verse.error = null;
        },

        onChapterChange(index) {
            // Reset verses when chapter changes
            const verse = this.verses[index];
            verse.end_chapter = null;
            verse.start_verse = null;
            verse.end_verse = null;
            verse.text = '';
            verse.error = null;
        },

        onEndChapterChange(index) {
            // When end_chapter is set, clear verse numbers (chapter range mode)
            const verse = this.verses[index];
            if (verse.end_chapter) {
                verse.start_verse = null;
                verse.end_verse = null;
            }
            verse.text = '';
            verse.error = null;
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleString();
        },
    },
};
</script>

<style scoped>
.bible-verse-finder {
    @apply space-y-6;
}

.bible-verse-card {
    @apply p-4 bg-white border border-gray-300 rounded shadow-sm;
}

.bible-verse-card.fetching {
    @apply opacity-60 pointer-events-none;
}

.bible-verse-card.error {
    @apply border-red-300 bg-red-50;
}

.verse-header {
    @apply mb-4 pb-3 border-b border-gray-200;
}

.verse-body {
    @apply space-y-4;
}

.error-message {
    @apply flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700;
}

.verse-preview {
    @apply mt-4 p-5 bg-gradient-to-br from-green-50 via-emerald-50 to-blue-50 border-2 border-green-400 rounded-lg shadow-sm;
}

.preview-header {
    @apply mb-3 pb-3 border-b-2 border-green-300;
}

.preview-text {
    @apply text-gray-900 leading-relaxed mb-3 text-base font-serif italic px-3 py-2 bg-white/50 rounded;
}

.preview-meta {
    @apply text-xs text-gray-600 flex items-center gap-1.5;
}

.btn-primary {
    @apply btn-primary;
}
</style>
