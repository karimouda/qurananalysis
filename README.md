# QuranAnalysis (QA) Project

# Introduction

The goal of this project is to build a Semantic Search and Intelligence System for the Quran, providing normal users and scholars the ability to search the Quran semantically, analyse all aspects of the text, find hidden patterns and associations using state-of-the-art visualization techniques.

**http://www.qurananalysis.com**

QA started as an MSc project at the University of Leeds in 2015 supervised by Eric Atwell. The project aimed to glu-together and build on previous research done in the university and to provide an opensource base for Quran Analysis work, It was released as an opensource project to facilitate Quranic/Arabic research, to boost applications and foster innovation in that area.

More information can be found in my Thesis below

[QuranAnalysis: A Semantic Search and Intelligence System for the Quran](https://www.researchgate.net/publication/282648776_QuranAnalysis_A_Semantic_Search_and_Intelligence_System_for_the_Quran)

Feel free to use QA in your research or applications, also contributions are welcome.


## How can you benefit from QA ?


Assuming you are a developer or researcher, with more than 20,000 lines of code you can benefit in many ways as explained below

- Access to language corpus and resources (inlcuding QA Ontology)
- Reusable code and algorithms
- Libraries for various tasks around Arabic Language handling, Data Model manipulation, NLP, Semantics, and Quran specifics
- QA Ontology extraction module
- Search Engine, Question Answering and Semantic Application Implementation
- Novel D3 visualzation techniques
- Speed up prototyping by reusing QA user interface

## QA Unique Resources

1. **QA Ontology**: OWL file including rich concepts, relations and metadata from the Quran
2. **Stopwords Lists**: Quranic stop words lists
3. **Simple to Uthmani Mapping File**: A file containing one-to-one mapping between simple and uthmani words from the Quran
4. **Qurana to QAC segment Mapping File**: QAC and Qurana has different segments counts. The file includes one-to-one mapping between QAC and Qurana segment numbers
5. **Longest Common Substrings in the Quran**: A file containing all common substrings in the Quran - extracted using LCS algorithm

## External Resources used in QA

1. Tanzil Project - Quran Text: Authentic Simple/Uthmani text of the the Quran 
2. Tanzil Project - Quran Translation: English translation corpus of the Quran 
3. Tanzil Project - Quran Transliteration: English transliteration corpus of the Quran 
4. Quranic Arabic Corpus: PoS tagged corpus of the Quran with morphological annotations 
5. Quranic Arabic Corpus Word-by-Word: Word by word Arabic-English translation corpus of the Quran (Edited & not up to date)
6. Qurana: Corpus of the Quran annotated with Pronominal Anaphora
7. Wordnet: English dictionary and thesaurus corpus 
8. DBPedia: semantic structured data extracted from Wikipedia 
9. D3 Javascript Library 
10. JQuery and JQuery Tagcloud Javascript Libraries.
11. TinySort Javascript Library
12. OWLLib PHP Library 
13. Microsoft Translator API 
14. PHPir PoS Tagging Library 
15. Brown corpus lexicon for English PoS Tagging 
16. English stop-words project 
17. OpenOffice ar.dic file 
18. Arabic stopwords list from Ar-PHP project
19. TextMiningTheQuran stop-words list
20. Limited number of basic English and Arabic stopwords taken from


## Libraries
* charts.lib.php
* core.lib.php
* custom.translation.table.lib.php
* graph.lib.php
* microsoft.translator.api.lib.php
* ontology.lib.php
* pos.tagger.lib.php
* question.answering.lib.php
* search.lib.php
* wordnet.lib.php
* owllib

List of functions can be found [here](https://github.com/karimouda/qurananalysis/wiki/Libraries-&-Functions)


## WIKI

https://github.com/karimouda/qurananalysis/wiki

## Tutorials

https://github.com/karimouda/qurananalysis/wiki/Tutorials

## Notes

I am sorry that some parts in the code are messy, undocumented and not well designed, this is due to shortage of time, stress and the novelty aspect of the project which lead me to focus more on research, coding and thesis writing to meet deadlines, i thought releasing the code this way is much better than waiting until i have time to clean it up.

## License

    Quran Analysis (www.qurananalysis.com). Full Semantic Search and Intelligence System for the Quran.
    Copyright (C) 2015  Karim Ouda

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    You can use Quran Analysis code, framework and corpora in your website
    or application (commercial/non-commercial) provided that you link
    back to www.qurananalysis.com and sufficient credits are given.
