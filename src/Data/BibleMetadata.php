<?php

namespace NewSong\BibleVerseFinder\Data;

class BibleMetadata
{
    /**
     * All 66 books of the Bible with their metadata
     *
     * Structure:
     * - name: Full book name
     * - testament: 'ot' or 'nt'
     * - chapters: Array of verse counts per chapter
     * - abbreviations: Common abbreviations for the book
     * - number: Book number (1-66)
     */
    public static function books(): array
    {
        return [
            'Genesis' => [
                'name' => 'Genesis',
                'testament' => 'ot',
                'number' => 1,
                'chapters' => [31, 25, 24, 26, 32, 22, 24, 22, 29, 32, 32, 20, 18, 24, 21, 16, 27, 33, 38, 18, 34, 24, 20, 67, 34, 35, 46, 22, 35, 43, 55, 32, 20, 31, 29, 43, 36, 30, 23, 23, 57, 38, 34, 34, 28, 34, 31, 22, 33, 26],
                'abbreviations' => ['Gen', 'Ge', 'Gn'],
            ],
            'Exodus' => [
                'name' => 'Exodus',
                'testament' => 'ot',
                'number' => 2,
                'chapters' => [22, 25, 22, 31, 23, 30, 25, 32, 35, 29, 10, 51, 22, 31, 27, 36, 16, 27, 25, 26, 36, 31, 33, 18, 40, 37, 21, 43, 46, 38, 18, 35, 23, 35, 35, 38, 29, 31, 43, 38],
                'abbreviations' => ['Exod', 'Ex', 'Exo'],
            ],
            'Leviticus' => [
                'name' => 'Leviticus',
                'testament' => 'ot',
                'number' => 3,
                'chapters' => [17, 16, 17, 35, 19, 30, 38, 36, 24, 20, 47, 8, 59, 57, 33, 34, 16, 30, 37, 27, 24, 33, 44, 23, 55, 46, 34],
                'abbreviations' => ['Lev', 'Le', 'Lv'],
            ],
            'Numbers' => [
                'name' => 'Numbers',
                'testament' => 'ot',
                'number' => 4,
                'chapters' => [54, 34, 51, 49, 31, 27, 89, 26, 23, 36, 35, 16, 33, 45, 41, 50, 13, 32, 22, 29, 35, 41, 30, 25, 18, 65, 23, 31, 40, 16, 54, 42, 56, 29, 34, 13],
                'abbreviations' => ['Num', 'Nu', 'Nm', 'Nb'],
            ],
            'Deuteronomy' => [
                'name' => 'Deuteronomy',
                'testament' => 'ot',
                'number' => 5,
                'chapters' => [46, 37, 29, 49, 33, 25, 26, 20, 29, 22, 32, 32, 18, 29, 23, 22, 20, 22, 21, 20, 23, 30, 25, 22, 19, 19, 26, 68, 29, 20, 30, 52, 29, 12],
                'abbreviations' => ['Deut', 'Dt', 'De'],
            ],
            'Joshua' => [
                'name' => 'Joshua',
                'testament' => 'ot',
                'number' => 6,
                'chapters' => [18, 24, 17, 24, 15, 27, 26, 35, 27, 43, 23, 24, 33, 15, 63, 10, 18, 28, 51, 9, 45, 34, 16, 33],
                'abbreviations' => ['Josh', 'Jos', 'Jsh'],
            ],
            'Judges' => [
                'name' => 'Judges',
                'testament' => 'ot',
                'number' => 7,
                'chapters' => [36, 23, 31, 24, 31, 40, 25, 35, 57, 18, 40, 15, 25, 20, 20, 31, 13, 31, 30, 48, 25],
                'abbreviations' => ['Judg', 'Jdg', 'Jg', 'Jdgs'],
            ],
            'Ruth' => [
                'name' => 'Ruth',
                'testament' => 'ot',
                'number' => 8,
                'chapters' => [22, 23, 18, 22],
                'abbreviations' => ['Rth', 'Ru'],
            ],
            '1 Samuel' => [
                'name' => '1 Samuel',
                'testament' => 'ot',
                'number' => 9,
                'chapters' => [28, 36, 21, 22, 12, 21, 17, 22, 27, 27, 15, 25, 23, 52, 35, 23, 58, 30, 24, 42, 15, 23, 29, 22, 44, 25, 12, 25, 11, 31, 13],
                'abbreviations' => ['1 Sam', '1 Sm', '1Sa', '1S', 'I Sa', '1 S', 'I Sam', '1Sam', 'I Samuel', '1Samuel'],
            ],
            '2 Samuel' => [
                'name' => '2 Samuel',
                'testament' => 'ot',
                'number' => 10,
                'chapters' => [27, 32, 39, 12, 25, 23, 29, 18, 13, 19, 27, 31, 39, 33, 37, 23, 29, 33, 43, 26, 22, 51, 39, 25],
                'abbreviations' => ['2 Sam', '2 Sm', '2Sa', '2S', 'II Sa', '2 S', 'II Sam', '2Sam', 'II Samuel', '2Samuel'],
            ],
            '1 Kings' => [
                'name' => '1 Kings',
                'testament' => 'ot',
                'number' => 11,
                'chapters' => [53, 46, 28, 34, 18, 38, 51, 66, 28, 29, 43, 33, 34, 31, 34, 34, 24, 46, 21, 43, 29, 53],
                'abbreviations' => ['1 Kgs', '1 Ki', '1K', '1Ki', 'I Ki', '1Ki', 'I Kings', '1Kings', '1st Kgs', '1st Kings'],
            ],
            '2 Kings' => [
                'name' => '2 Kings',
                'testament' => 'ot',
                'number' => 12,
                'chapters' => [18, 25, 27, 44, 27, 33, 20, 29, 37, 36, 21, 21, 25, 29, 38, 20, 41, 37, 37, 21, 26, 20, 37, 20, 30],
                'abbreviations' => ['2 Kgs', '2 Ki', '2K', '2Ki', 'II Ki', '2Ki', 'II Kings', '2Kings', '2nd Kgs', '2nd Kings'],
            ],
            '1 Chronicles' => [
                'name' => '1 Chronicles',
                'testament' => 'ot',
                'number' => 13,
                'chapters' => [54, 55, 24, 43, 26, 81, 40, 40, 44, 14, 47, 40, 14, 17, 29, 43, 27, 17, 19, 8, 30, 19, 32, 31, 31, 32, 34, 21, 30],
                'abbreviations' => ['1 Chron', '1 Ch', '1Ch', 'I Ch', 'I Chr', '1Chr', '1Chron', 'I Chron', '1Chron'],
            ],
            '2 Chronicles' => [
                'name' => '2 Chronicles',
                'testament' => 'ot',
                'number' => 14,
                'chapters' => [17, 18, 17, 22, 14, 42, 22, 18, 31, 19, 23, 16, 22, 15, 19, 14, 19, 34, 11, 37, 20, 12, 21, 27, 28, 23, 9, 27, 36, 27, 21, 33, 25, 33, 27, 23],
                'abbreviations' => ['2 Chron', '2 Ch', '2Ch', 'II Ch', 'II Chr', '2Chr', '2Chron', 'II Chron', '2Chron'],
            ],
            'Ezra' => [
                'name' => 'Ezra',
                'testament' => 'ot',
                'number' => 15,
                'chapters' => [11, 70, 13, 24, 17, 22, 28, 36, 15, 44],
                'abbreviations' => ['Ezr', 'Ez'],
            ],
            'Nehemiah' => [
                'name' => 'Nehemiah',
                'testament' => 'ot',
                'number' => 16,
                'chapters' => [11, 20, 32, 23, 19, 19, 73, 18, 38, 39, 36, 47, 31],
                'abbreviations' => ['Neh', 'Ne'],
            ],
            'Esther' => [
                'name' => 'Esther',
                'testament' => 'ot',
                'number' => 17,
                'chapters' => [22, 23, 15, 17, 14, 14, 10, 17, 32, 3],
                'abbreviations' => ['Esth', 'Est', 'Es'],
            ],
            'Job' => [
                'name' => 'Job',
                'testament' => 'ot',
                'number' => 18,
                'chapters' => [22, 13, 26, 21, 27, 30, 21, 22, 35, 22, 20, 25, 28, 22, 35, 22, 16, 21, 29, 29, 34, 30, 17, 25, 6, 14, 23, 28, 25, 31, 40, 22, 33, 37, 16, 33, 24, 41, 30, 24, 34, 17],
                'abbreviations' => ['Jb'],
            ],
            'Psalms' => [
                'name' => 'Psalms',
                'testament' => 'ot',
                'number' => 19,
                'chapters' => [6, 12, 8, 8, 12, 10, 17, 9, 20, 18, 7, 8, 6, 7, 5, 11, 15, 50, 14, 9, 13, 31, 6, 10, 22, 12, 14, 9, 11, 12, 24, 11, 22, 22, 28, 12, 40, 22, 13, 17, 13, 11, 5, 26, 17, 11, 9, 14, 20, 23, 19, 9, 6, 7, 23, 13, 11, 11, 17, 12, 8, 12, 11, 10, 13, 20, 7, 35, 36, 5, 24, 20, 28, 23, 10, 12, 20, 72, 13, 19, 16, 8, 18, 12, 13, 17, 7, 18, 52, 17, 16, 15, 5, 23, 11, 13, 12, 9, 9, 5, 8, 28, 22, 35, 45, 48, 43, 13, 31, 7, 10, 10, 9, 8, 18, 19, 2, 29, 176, 7, 8, 9, 4, 8, 5, 6, 5, 6, 8, 8, 3, 18, 3, 3, 21, 26, 9, 8, 24, 13, 10, 7, 12, 15, 21, 10, 20, 14, 9, 6],
                'abbreviations' => ['Ps', 'Psalm', 'Pslm', 'Psa', 'Psm', 'Pss'],
            ],
            'Proverbs' => [
                'name' => 'Proverbs',
                'testament' => 'ot',
                'number' => 20,
                'chapters' => [33, 22, 35, 27, 23, 35, 27, 36, 18, 32, 31, 28, 25, 35, 33, 33, 28, 24, 29, 30, 31, 29, 35, 34, 28, 28, 27, 28, 27, 33, 31],
                'abbreviations' => ['Prov', 'Pro', 'Prv', 'Pr'],
            ],
            'Ecclesiastes' => [
                'name' => 'Ecclesiastes',
                'testament' => 'ot',
                'number' => 21,
                'chapters' => [18, 26, 22, 16, 20, 12, 29, 17, 18, 20, 10, 14],
                'abbreviations' => ['Eccles', 'Eccle', 'Ecc', 'Ec', 'Qoh'],
            ],
            'Song of Solomon' => [
                'name' => 'Song of Solomon',
                'testament' => 'ot',
                'number' => 22,
                'chapters' => [17, 17, 11, 16, 16, 13, 13, 14],
                'abbreviations' => ['Song', 'Song of Songs', 'SOS', 'So', 'Canticle of Canticles', 'Canticles', 'Cant'],
            ],
            'Isaiah' => [
                'name' => 'Isaiah',
                'testament' => 'ot',
                'number' => 23,
                'chapters' => [31, 22, 26, 6, 30, 13, 25, 22, 21, 34, 16, 6, 22, 32, 9, 14, 14, 7, 25, 6, 17, 25, 18, 23, 12, 21, 13, 29, 24, 33, 9, 20, 24, 17, 10, 22, 38, 22, 8, 31, 29, 25, 28, 28, 25, 13, 15, 22, 26, 11, 23, 15, 12, 17, 13, 12, 21, 14, 21, 22, 11, 12, 19, 12, 25, 24],
                'abbreviations' => ['Isa', 'Is'],
            ],
            'Jeremiah' => [
                'name' => 'Jeremiah',
                'testament' => 'ot',
                'number' => 24,
                'chapters' => [19, 37, 25, 31, 31, 30, 34, 22, 26, 25, 23, 17, 27, 22, 21, 21, 27, 23, 15, 18, 14, 30, 40, 10, 38, 24, 22, 17, 32, 24, 40, 44, 26, 22, 19, 32, 21, 28, 18, 16, 18, 22, 13, 30, 5, 28, 7, 47, 39, 46, 64, 34],
                'abbreviations' => ['Jer', 'Je', 'Jr'],
            ],
            'Lamentations' => [
                'name' => 'Lamentations',
                'testament' => 'ot',
                'number' => 25,
                'chapters' => [22, 22, 66, 22, 22],
                'abbreviations' => ['Lam', 'La'],
            ],
            'Ezekiel' => [
                'name' => 'Ezekiel',
                'testament' => 'ot',
                'number' => 26,
                'chapters' => [28, 10, 27, 17, 17, 14, 27, 18, 11, 22, 25, 28, 23, 23, 8, 63, 24, 32, 14, 49, 32, 31, 49, 27, 17, 21, 36, 26, 21, 26, 18, 32, 33, 31, 15, 38, 28, 23, 29, 49, 26, 20, 27, 31, 25, 24, 23, 35],
                'abbreviations' => ['Ezek', 'Eze', 'Ezk'],
            ],
            'Daniel' => [
                'name' => 'Daniel',
                'testament' => 'ot',
                'number' => 27,
                'chapters' => [21, 49, 30, 37, 31, 28, 28, 27, 27, 21, 45, 13],
                'abbreviations' => ['Dan', 'Da', 'Dn'],
            ],
            'Hosea' => [
                'name' => 'Hosea',
                'testament' => 'ot',
                'number' => 28,
                'chapters' => [11, 23, 5, 19, 15, 11, 16, 14, 17, 15, 12, 14, 16, 9],
                'abbreviations' => ['Hos', 'Ho'],
            ],
            'Joel' => [
                'name' => 'Joel',
                'testament' => 'ot',
                'number' => 29,
                'chapters' => [20, 32, 21],
                'abbreviations' => ['Joe', 'Jl'],
            ],
            'Amos' => [
                'name' => 'Amos',
                'testament' => 'ot',
                'number' => 30,
                'chapters' => [15, 16, 15, 13, 27, 14, 17, 14, 15],
                'abbreviations' => ['Am'],
            ],
            'Obadiah' => [
                'name' => 'Obadiah',
                'testament' => 'ot',
                'number' => 31,
                'chapters' => [21],
                'abbreviations' => ['Obad', 'Ob'],
            ],
            'Jonah' => [
                'name' => 'Jonah',
                'testament' => 'ot',
                'number' => 32,
                'chapters' => [17, 10, 10, 11],
                'abbreviations' => ['Jnh', 'Jon'],
            ],
            'Micah' => [
                'name' => 'Micah',
                'testament' => 'ot',
                'number' => 33,
                'chapters' => [16, 13, 12, 13, 15, 16, 20],
                'abbreviations' => ['Mic', 'Mc'],
            ],
            'Nahum' => [
                'name' => 'Nahum',
                'testament' => 'ot',
                'number' => 34,
                'chapters' => [15, 13, 19],
                'abbreviations' => ['Nah', 'Na'],
            ],
            'Habakkuk' => [
                'name' => 'Habakkuk',
                'testament' => 'ot',
                'number' => 35,
                'chapters' => [17, 20, 19],
                'abbreviations' => ['Hab', 'Hb'],
            ],
            'Zephaniah' => [
                'name' => 'Zephaniah',
                'testament' => 'ot',
                'number' => 36,
                'chapters' => [18, 15, 20],
                'abbreviations' => ['Zeph', 'Zep', 'Zp'],
            ],
            'Haggai' => [
                'name' => 'Haggai',
                'testament' => 'ot',
                'number' => 37,
                'chapters' => [15, 23],
                'abbreviations' => ['Hag', 'Hg'],
            ],
            'Zechariah' => [
                'name' => 'Zechariah',
                'testament' => 'ot',
                'number' => 38,
                'chapters' => [21, 13, 10, 14, 11, 15, 14, 23, 17, 12, 17, 14, 9, 21],
                'abbreviations' => ['Zech', 'Zec', 'Zc'],
            ],
            'Malachi' => [
                'name' => 'Malachi',
                'testament' => 'ot',
                'number' => 39,
                'chapters' => [14, 17, 18, 6],
                'abbreviations' => ['Mal', 'Ml'],
            ],
            'Matthew' => [
                'name' => 'Matthew',
                'testament' => 'nt',
                'number' => 40,
                'chapters' => [25, 23, 17, 25, 48, 34, 29, 34, 38, 42, 30, 50, 58, 36, 39, 28, 27, 35, 30, 34, 46, 46, 39, 51, 46, 75, 66, 20],
                'abbreviations' => ['Matt', 'Mat', 'Mt'],
            ],
            'Mark' => [
                'name' => 'Mark',
                'testament' => 'nt',
                'number' => 41,
                'chapters' => [45, 28, 35, 41, 43, 56, 37, 38, 50, 52, 33, 44, 37, 72, 47, 20],
                'abbreviations' => ['Mrk', 'Mar', 'Mk', 'Mr'],
            ],
            'Luke' => [
                'name' => 'Luke',
                'testament' => 'nt',
                'number' => 42,
                'chapters' => [80, 52, 38, 44, 39, 49, 50, 56, 62, 42, 54, 59, 35, 35, 32, 31, 37, 43, 48, 47, 38, 71, 56, 53],
                'abbreviations' => ['Luk', 'Lk'],
            ],
            'John' => [
                'name' => 'John',
                'testament' => 'nt',
                'number' => 43,
                'chapters' => [51, 25, 36, 54, 47, 71, 53, 59, 41, 42, 57, 50, 38, 31, 27, 33, 26, 40, 42, 31, 25],
                'abbreviations' => ['Jhn', 'Joh', 'Jn'],
            ],
            'Acts' => [
                'name' => 'Acts',
                'testament' => 'nt',
                'number' => 44,
                'chapters' => [26, 47, 26, 37, 42, 15, 60, 40, 43, 48, 30, 25, 52, 28, 41, 40, 34, 28, 41, 38, 40, 30, 35, 27, 27, 32, 44, 31],
                'abbreviations' => ['Act', 'Ac'],
            ],
            'Romans' => [
                'name' => 'Romans',
                'testament' => 'nt',
                'number' => 45,
                'chapters' => [32, 29, 31, 25, 21, 23, 25, 39, 33, 21, 36, 21, 14, 23, 33, 27],
                'abbreviations' => ['Rom', 'Ro', 'Rm'],
            ],
            '1 Corinthians' => [
                'name' => '1 Corinthians',
                'testament' => 'nt',
                'number' => 46,
                'chapters' => [31, 16, 23, 21, 13, 20, 40, 13, 27, 33, 34, 31, 13, 40, 58, 24],
                'abbreviations' => ['1 Cor', '1 Co', '1Co', 'I Co', 'I Cor', '1Cor', 'I Corinthians', '1Corinthians', '1st Corinthians'],
            ],
            '2 Corinthians' => [
                'name' => '2 Corinthians',
                'testament' => 'nt',
                'number' => 47,
                'chapters' => [24, 17, 18, 18, 21, 18, 16, 24, 15, 18, 33, 21, 14],
                'abbreviations' => ['2 Cor', '2 Co', '2Co', 'II Co', 'II Cor', '2Cor', 'II Corinthians', '2Corinthians', '2nd Corinthians'],
            ],
            'Galatians' => [
                'name' => 'Galatians',
                'testament' => 'nt',
                'number' => 48,
                'chapters' => [24, 21, 29, 31, 26, 18],
                'abbreviations' => ['Gal', 'Ga'],
            ],
            'Ephesians' => [
                'name' => 'Ephesians',
                'testament' => 'nt',
                'number' => 49,
                'chapters' => [23, 22, 21, 32, 33, 24],
                'abbreviations' => ['Eph', 'Ephes'],
            ],
            'Philippians' => [
                'name' => 'Philippians',
                'testament' => 'nt',
                'number' => 50,
                'chapters' => [30, 30, 21, 23],
                'abbreviations' => ['Phil', 'Php', 'Pp'],
            ],
            'Colossians' => [
                'name' => 'Colossians',
                'testament' => 'nt',
                'number' => 51,
                'chapters' => [29, 23, 25, 18],
                'abbreviations' => ['Col', 'Co'],
            ],
            '1 Thessalonians' => [
                'name' => '1 Thessalonians',
                'testament' => 'nt',
                'number' => 52,
                'chapters' => [10, 20, 13, 18, 28],
                'abbreviations' => ['1 Thess', '1 Th', '1Th', 'I Th', 'I Thes', 'I Thess', '1Thess', '1Thes', 'I Thessalonians', '1Thessalonians', '1st Thessalonians'],
            ],
            '2 Thessalonians' => [
                'name' => '2 Thessalonians',
                'testament' => 'nt',
                'number' => 53,
                'chapters' => [12, 17, 18],
                'abbreviations' => ['2 Thess', '2 Th', '2Th', 'II Th', 'II Thes', 'II Thess', '2Thess', '2Thes', 'II Thessalonians', '2Thessalonians', '2nd Thessalonians'],
            ],
            '1 Timothy' => [
                'name' => '1 Timothy',
                'testament' => 'nt',
                'number' => 54,
                'chapters' => [20, 15, 16, 16, 25, 21],
                'abbreviations' => ['1 Tim', '1 Ti', '1Ti', 'I Ti', 'I Tim', '1Tim', 'I Timothy', '1Timothy', '1st Timothy'],
            ],
            '2 Timothy' => [
                'name' => '2 Timothy',
                'testament' => 'nt',
                'number' => 55,
                'chapters' => [18, 26, 17, 22],
                'abbreviations' => ['2 Tim', '2 Ti', '2Ti', 'II Ti', 'II Tim', '2Tim', 'II Timothy', '2Timothy', '2nd Timothy'],
            ],
            'Titus' => [
                'name' => 'Titus',
                'testament' => 'nt',
                'number' => 56,
                'chapters' => [16, 15, 15],
                'abbreviations' => ['Tit', 'Ti'],
            ],
            'Philemon' => [
                'name' => 'Philemon',
                'testament' => 'nt',
                'number' => 57,
                'chapters' => [25],
                'abbreviations' => ['Philem', 'Phm', 'Pm'],
            ],
            'Hebrews' => [
                'name' => 'Hebrews',
                'testament' => 'nt',
                'number' => 58,
                'chapters' => [14, 18, 19, 16, 14, 20, 28, 13, 28, 39, 40, 29, 25],
                'abbreviations' => ['Heb'],
            ],
            'James' => [
                'name' => 'James',
                'testament' => 'nt',
                'number' => 59,
                'chapters' => [27, 26, 18, 17, 20],
                'abbreviations' => ['Jas', 'Jm'],
            ],
            '1 Peter' => [
                'name' => '1 Peter',
                'testament' => 'nt',
                'number' => 60,
                'chapters' => [25, 25, 22, 19, 14],
                'abbreviations' => ['1 Pet', '1 Pe', '1P', 'I Pe', 'I Pet', '1Pet', 'I Pt', 'I Peter', '1Peter', '1st Peter'],
            ],
            '2 Peter' => [
                'name' => '2 Peter',
                'testament' => 'nt',
                'number' => 61,
                'chapters' => [21, 22, 18],
                'abbreviations' => ['2 Pet', '2 Pe', '2P', 'II Pe', 'II Pet', '2Pet', 'II Pt', 'II Peter', '2Peter', '2nd Peter'],
            ],
            '1 John' => [
                'name' => '1 John',
                'testament' => 'nt',
                'number' => 62,
                'chapters' => [10, 29, 24, 21, 21],
                'abbreviations' => ['1 Jn', '1J', 'I Jn', 'I Jo', 'I Joh', 'I John', '1John', '1st John'],
            ],
            '2 John' => [
                'name' => '2 John',
                'testament' => 'nt',
                'number' => 63,
                'chapters' => [13],
                'abbreviations' => ['2 Jn', '2J', 'II Jn', 'II Jo', 'II Joh', 'II John', '2John', '2nd John'],
            ],
            '3 John' => [
                'name' => '3 John',
                'testament' => 'nt',
                'number' => 64,
                'chapters' => [14],
                'abbreviations' => ['3 Jn', '3J', 'III Jn', 'III Jo', 'III Joh', 'III John', '3John', '3rd John'],
            ],
            'Jude' => [
                'name' => 'Jude',
                'testament' => 'nt',
                'number' => 65,
                'chapters' => [25],
                'abbreviations' => ['Jud', 'Jd'],
            ],
            'Revelation' => [
                'name' => 'Revelation',
                'testament' => 'nt',
                'number' => 66,
                'chapters' => [20, 29, 22, 11, 14, 17, 17, 13, 21, 11, 19, 17, 18, 20, 8, 21, 18, 24, 21, 15, 27, 21],
                'abbreviations' => ['Rev', 'Re', 'The Revelation'],
            ],
        ];
    }

    /**
     * Get a book by name or abbreviation
     */
    public static function findBook(string $bookName): ?array
    {
        $bookName = trim($bookName);
        $books = self::books();

        // Direct match
        if (isset($books[$bookName])) {
            return $books[$bookName];
        }

        // Case-insensitive match
        foreach ($books as $name => $data) {
            if (strcasecmp($name, $bookName) === 0) {
                return $data;
            }
        }

        // Abbreviation match
        foreach ($books as $name => $data) {
            foreach ($data['abbreviations'] as $abbr) {
                if (strcasecmp($abbr, $bookName) === 0) {
                    return $data;
                }
            }
        }

        return null;
    }

    /**
     * Get book list for dropdown
     */
    public static function getBookList(): array
    {
        $books = self::books();
        $list = [];

        foreach ($books as $name => $data) {
            $list[] = [
                'value' => $name,
                'label' => $name,
                'testament' => $data['testament'],
            ];
        }

        return $list;
    }

    /**
     * Validate if a chapter exists in a book
     */
    public static function validateChapter(string $bookName, int $chapter): bool
    {
        $book = self::findBook($bookName);

        if (!$book) {
            return false;
        }

        return $chapter > 0 && $chapter <= count($book['chapters']);
    }

    /**
     * Validate if a verse exists in a chapter
     */
    public static function validateVerse(string $bookName, int $chapter, int $verse): bool
    {
        $book = self::findBook($bookName);

        if (!$book || !self::validateChapter($bookName, $chapter)) {
            return false;
        }

        $verseCount = $book['chapters'][$chapter - 1];
        return $verse > 0 && $verse <= $verseCount;
    }

    /**
     * Get the number of verses in a chapter
     */
    public static function getVerseCount(string $bookName, int $chapter): ?int
    {
        $book = self::findBook($bookName);

        if (!$book || !self::validateChapter($bookName, $chapter)) {
            return null;
        }

        return $book['chapters'][$chapter - 1];
    }

    /**
     * Get the number of chapters in a book
     */
    public static function getChapterCount(string $bookName): ?int
    {
        $book = self::findBook($bookName);

        if (!$book) {
            return null;
        }

        return count($book['chapters']);
    }
}
