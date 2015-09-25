<div class='analysis-help-area'>	
<img  src='/images/help-icon-2.png' class='help-icon analyze-help-icon'  onclick="showHelpMessage('analysis-help-message','ANALYSIS','<?=$_SERVER['PHP_SELF']?>')"/>
<div id='analysis-help-message' class='help-message-area'>
	<b>Help</b> 
	<?php 

	
	?>
	<?php if (strpos($_SERVER['PHP_SELF'],"/basic-statistics.php")!==false):?>
	This page shows much statistics from the Quran for both Arabic text and English translation.
	<br>
	<br>
	 Statistics shown includes the following:
	<ol>
		<li>The total number of chapters, verses, words and characters.</li>
		<li>Minimum and maximum words, verses and word/verse lengths.</li>
		<li>Breakdown of totals by chapters.</li>
		<li>Quran pause marks count.</li>
	</ol>
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/word-frequency.php")!==false):?>
	<p>
		This tool lists all words in the Quran with their frequencies and weights calculated using the TFIDF algorithm. Each chapter is considered a “Document” in TFIDF calculation. The tool also provides a button to exclude stop words from the list.
	</p>
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/word-clouds.php")!==false):?>
	<p>
	This tool shows word clouds for each chapter in the Quran in addition to 2 other clouds for verse endings and beginnings (clouds for first and last words in each verse). The bigger the word size the more it is mentioned in the Quran.
	</p>	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/full-quran-text.php")!==false):?>
	<p>
	This page lists all verses in the Quran in order so you can see the source text used in this website in one page.
	</p>		
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/charts.php")!==false):?>
	<p>
	This page shows a collection of charts from Quranic data. 
	<br>
	<br>
	Chapter/Verse distribution shows how many verses are in each chapter in the Quran.
	</p>	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/ngrams.php")!==false):?>
	<p>
	The n-grams tool gives you the ability to choose the "N" value in n-grams and produces a list of N-gram words from the Quran	
	<br>
	<a target='_NEW' href='https://en.wikipedia.org/wiki/N-gram'>https://en.wikipedia.org/wiki/N-gram</a>
	</p>
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/pos-ngrams.php")!==false):?>
	<p>
	This unique tool gives you the ability to get verses from the Quran matching a specific <a target='_NEW' href='https://en.wikipedia.org/wiki/Part_of_speech'>PoS</a> Pattern, for example if the user specified “PN V” the tool will return all verses having a "proper noun" followed by a "verb". 
	<br/>
	<br/>
	Such tool is very useful in choosing syntactic and lexico-syntactic patterns
	<br/>
	<br/>
	The tool supports all <a target='_NEW' href='http://corpus.quran.com/documentation/tagset.jsp'>QAC tags</a> in addition to “*” wildcard.
	<br/>
	</p>
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/part-of-speech.php")!==false):?>
	<p>
	This tool lists verses containing any specific <a target='_NEW' href='https://en.wikipedia.org/wiki/Part_of_speech'>PoS</a> Tag from the Quran.
	<br/>
	<br/>
	<a target='_NEW' href='http://corpus.quran.com/documentation/tagset.jsp'>QAC tagset</a> is supported.
	<br/>
	<br/>
	 The tool also supports filtering by <a target='_NEW' href='http://corpus.quran.com/documentation/tagset.jsp'>QAC features</a> for example the user can search for “N” as a PoS and “GEN” as a feature, the tool will return verses containing a noun in a genitive case.
	<br/>
	<br/>
	The tool will also show the number of verses and all “distinct” words for the specified PoS Tag.
	<br/>
	</p>
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/repetition-verses.php")!==false):?>
	<p>
	This page shows all repeated verses from the Quran. Verses are sorted in a descending order by frequency
	</p>	
	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/repetition-common-substrings.php")!==false):?>
	<p>
	This page shows all repeated “phrases” (sub-verses or substring of verses) from the Quran. Phrases are sorted in a descending order by frequency.
	<br/>
	<br/>
	<a target='_NEW' href='https://en.wikipedia.org/wiki/Longest_common_substring_problem'>LCS (Longest Common Substrings) algorithm</a> was applied on the whole text of the Quran to make up this list.
	</p>
	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/ontology.php")!==false):?>
	<p>
		This page shows the data extracted from QA ontology. All concepts and relations are shown in tables including their totals.	
	<br/>
	<br/>
		The motiviation is to check specific relations or concepts from the ontology online without using OWL ontology viewing tools.
	</p>
	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/ontology-graph.php")!==false):?>
	<p>
		This tool shows the subset ontology of any selected chapter in the Quran in addition to the visualization of the full QA ontology.
		<br/>
		<br/>
		The importance of the subset ontology for chapters is that it can be considered a “footprint” or a “digest” for any chapter since it shows the “concepts” mentioned in the chapter in variable sizes according to their frequency and the links between them
		 for example “The Iron” chapter has more emphasis on heaven, rewards, bounty, light, life, people and messengers.
	</p>
	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/uthmani-to-simple.php")!==false):?>
	<p>
		The Quran is written in uthmani script which is different from the simple script used in modern Arabic at present. 
		<br>
		<br>
		This page shows all uthmani words in the Quran and their corresponding simple words.
	</p>		
			
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/words-information.php")!==false):?>
	<p>
		This tool provides information about any Arabic word in the Quran by gathering data about the word from all relevant data models in QA. For each word the following is shown to the user:
		<br/>
		<ol>
			<li>Simple and Uthmani word presentations</li>
			<li>Frequency</li>
			<li>TF-IDF Weight</li>
			<li>Buckwalter Transliteration</li>
			<li>Transliteration</li>
			<li>English Translation</li>
			<li>Word Root</li>
			<li>Word Lemma</li>
			<li>QAC PoS Tags</li>
			<li>QAC Features</li>
			<li>Verses</li>
		</ol>
	</p>	
	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/word-context-collocation.php")!==false):?>
	<p>

		The <a target='_NEW' href='https://en.wikipedia.org/wiki/Collocation'>collocation</a> tool shows the context of any word in the Quran. When a word is entered by the user, the tool will show all words mentioned before or after the target word up to 3 levels.
		
		<br/>
		<br/>
		The tool also supports <a target='_NEW' href='http://corpus.quran.com/documentation/tagset.jsp'>QAC PoS tags</a> (Collocation of PoS Tags) which is a novel feature that can help in finding linguistic rules and patterns to facilitate various research tasks.
	</p>		
						
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/word-context-concordance.php")!==false):?>
	<p>
		The <a target='_NEW' href='https://en.wikipedia.org/wiki/Concordance'>concordance</a> tool shows the context of any word in the Quran with word dependencies considered.
		 When the user enter a word the tool will show all words mentioned before or after the target word up to N levels where N is chosen by the user.
		 <br>
		 <br>
		  The target word is highlighted in red and the words before and after are also highlighted but in blue.
		   The tool also supports <a target='_NEW' href='http://corpus.quran.com/documentation/tagset.jsp'>QAC PoS tags</a>.
		
		<br/>
		<br/>
		Another novel feature in this tool is that it shows the most repeated phrases before and after the target word, for example if the target word is “eats” and the specified context level is 3 the tool will show the most repeated trigrams (including target) such as “A and B eats” and “C and D eats” as “pre-context” and  “eats X Y” and “eats Y Z” as “post-context”.
 	 </p>	
 	 	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/pause-marks.php")!==false):?>
	<p>
		This tool will show all verses containing any chosen pause mark by the user. Pause marks are a set of 6 marks which directs the reciter of the Quran on when it is permissible, recommended or not acceptable to stop while reading.
		
 	 </p>		
					
	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/buckwalter-transliteration.php")!==false):?>
	<p>
	<a target='_NEW' href='https://en.wikipedia.org/wiki/Buckwalter_transliteration'>Buckwalter transliteration</a> is a reversible transliteration scheme used to write Arabic characters using Latin ASCII characters.
	<br/>
	<br/>
	<a target='_NEW' href='http://corpus.quran.com'>QAC</a> data is encoded using an extended version of <a target='_NEW' href='http://corpus.quran.com/java/buckwalter.jsp'>Buckwalter transliteration table</a> so a mapping function was needed to translate Arabic to Buckwalter and vice-versa in order to convert QAC segments to Arabic characters.

	<br/>
	<br/>
	The tool accepts Arabic or Buckwalter encoded string, it manage to detect the type of the string automatically and will show the result after conversion. 	
	
	</p>			
		
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/simmilar-words.php")!==false):?>
	<p>
	This tool shows the top 20 similar words (character similarity not "semantic meaning") for any word in the Quran. The tool supports both Arabic and English. The same function is used in QA for query suggestions.
	<br/>
	<br/>
	The words are found using an extended <a target='_NEW' href='https://en.wikipedia.org/wiki/Levenshtein_distance'>min-edit-distance algorithm</a>.
	</p>
	
	<?php elseif (strpos($_SERVER['PHP_SELF'],"/quran-initials.php")!==false):?>
	<p>
	
	<a target='_NEW' href='https://en.wikipedia.org/wiki/Muqatta%27at'>Quran Initials</a> are unique dis-joined letters which are found in the Quran in 30 locations. The letters are treated as one unit and in some cases it make up a full verse and in other cases they are found at the beginning of a long verse. The meaning of those “letter units” are not clear until present time and no one can claim having absolute understanding for any of them.
	<br/>
	<br/>
	The tool employs visualization and analytics aiming to help in deciphering the meaning of those letters. The tool shows the following:
	<br>
	<ol>
	<li>Totals for each unique initial.</li>
	<li>A chart showing distribution of initials in the Quran.</li>
	<li>A cloud of words found in the same verses of the initials.</li>
	<li>List of all verses - initials marked in blue and second word marked in red.</li>
	</ol>
	</p>
			
		
		
		
		
	<?php endif;?>
</div>
</div>	