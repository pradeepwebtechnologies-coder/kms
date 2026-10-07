<?php
/**
 * Starter content built only from information already published on
 * krishnamusicschool.com (October 2026), restructured and de-duplicated.
 *
 * Where the live site contradicted itself, the most consistent value was used
 * and listed in docs/02-FACTS-TO-CONFIRM.md. Placeholders such as {founder} and
 * {price_single} are filled from KMS Facts and the class price fields, so the
 * text stays correct when a fact changes.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block-editor paragraph.
 *
 * @param string $html Inner HTML.
 * @return string
 */
function kms_sc_p( $html ) {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
}

/**
 * Block-editor heading.
 *
 * @param string $text  Text.
 * @param int    $level Level.
 * @return string
 */
function kms_sc_h( $text, $level = 2 ) {
	$attr = 2 === $level ? '' : ' {"level":' . $level . '}';
	return "<!-- wp:heading{$attr} -->\n<h{$level} class=\"wp-block-heading\">{$text}</h{$level}>\n<!-- /wp:heading -->\n\n";
}

/**
 * Block-editor list.
 *
 * @param string[] $items Items (HTML).
 * @param bool     $ordered Ordered list.
 * @return string
 */
function kms_sc_list( $items, $ordered = false ) {
	$tag   = $ordered ? 'ol' : 'ul';
	$attr  = $ordered ? ' {"ordered":true}' : '';
	$inner = '';
	foreach ( $items as $item ) {
		$inner .= "<!-- wp:list-item -->\n<li>{$item}</li>\n<!-- /wp:list-item -->\n";
	}
	return "<!-- wp:list{$attr} -->\n<{$tag} class=\"wp-block-list\">{$inner}</{$tag}>\n<!-- /wp:list -->\n\n";
}

/**
 * Block-editor shortcode block.
 *
 * @param string $code Shortcode.
 * @return string
 */
function kms_sc_code( $code ) {
	return "<!-- wp:shortcode -->\n{$code}\n<!-- /wp:shortcode -->\n\n";
}

/**
 * Classes.
 *
 * @return array<int, array<string, mixed>>
 */
function kms_starter_courses() {
	return array(
		array(
			'slug'    => 'harmonium',
			'title'   => 'Online Harmonium Classes',
			'excerpt' => 'Live one-to-one harmonium lessons for beginners, yoga teachers and kirtan leaders: play and sing 4–5 bhajans or kirtans within 10 classes.',
			'order'   => 1,
			'meta'    => array(
				'kms_icon'         => 'piano',
				'kms_tagline'      => 'Learn to play harmonium and accompany your own singing, from your very first note.',
				'kms_answer'       => 'Live one-to-one harmonium classes on {platforms} with {founder}, for complete beginners, yoga teachers and kirtan leaders. Within 10 classes most students can play and sing 4–5 bhajans or kirtans with confidence. Each online class lasts {minutes} minutes; a single class costs {price_single} and 10 classes cost {price_pack10}.',
				'kms_audience'     => "Complete beginners — no musical background needed\nYoga teachers who want to lead chanting or kirtan in class\nKirtan leaders who want to accompany themselves\nBhajan singers and spiritual seekers drawn to mantra",
				'kms_level'        => 'Complete beginner to intermediate',
				'kms_outcomes'     => "Sit well, pump the bellows smoothly and find Sa, your home note\nPlay Sa Re Ga Ma scales and a steady drone\nAccompany your own voice with simple chords\nPlay and sing 4–5 bhajans or kirtans, such as Raghupati Raghav Raja Ram and Hare Krishna\nChant mantras such as the Gayatri Mantra with correct pronunciation\nKeep a 15–20 minute daily practice routine",
				'kms_requirements' => "A harmonium (a good student instrument costs about ₹3,000–8,000 in India; we can advise you on buying or renting one)\nA laptop or tablet with Zoom or Google Meet and a stable internet connection\nA quiet space; headphones help",
				'kms_modes'        => 'online,inperson',
				'kms_price_single' => 1500,
				'kms_price_pack5'  => 6500,
				'kms_price_pack10' => 13500,
				'kms_teaches'      => 'Harmonium technique, Bellows control, Drone and chord accompaniment, Bhajan and kirtan accompaniment, Mantra chanting, Raga basics',
				'kms_wa_text'      => 'Hi! I would like to learn harmonium online. Can we book the free consultation?',
			),
			'content' => kms_sc_h( 'What you learn, class by class' )
				. kms_sc_p( 'The 10-class course takes you from your first note to leading a simple kirtan. Single classes follow the same path at your own pace.' )
				. kms_sc_h( 'Foundation: classes 1–5', 3 )
				. kms_sc_list(
					array(
						'<strong>Instrument basics.</strong> Parts of the harmonium, posture and hand position, smooth bellows pumping, finding Sa and playing your first scale.',
						'<strong>Drone and first chords.</strong> Holding Sa or Sa–Pa as a drone, simple chords, right-hand melody over a left-hand drone, and Om chanting over the drone.',
						'<strong>Mantra pronunciation and rhythm.</strong> Sanskrit vowels and consonants, breath-aligned chanting, and the Gayatri Mantra with harmonium.',
						'<strong>Accompanying a bhajan.</strong> Two or three simple bhajans such as Raghupati Raghav Raja Ram and Hare Krishna Hare Rama, common chord patterns and call-and-response.',
						'<strong>Mantra meditation.</strong> 108 repetitions, meditative and celebratory tempos, and a 15–20 minute daily practice designed for you.',
					),
					true
				)
				. kms_sc_h( 'Intermediate: classes 6–10', 3 )
				. kms_sc_list(
					array(
						'<strong>Ragas in devotional music.</strong> Yaman (evening), Bhairav (morning) and Bhairavi (any time); Om Namah Shivaya in Raga Bhairav.',
						'<strong>Tala.</strong> Beats and rhythmic cycles, Teentaal (16 beats), and keeping time while you play.',
						'<strong>Advanced chanting.</strong> Alaap before a mantra, the three octaves (mandra, madhya and taar) and the Mahamrityunjaya Mantra with ornaments.',
						'<strong>Leading kirtan.</strong> Leading and following, building energy from slow to fast, teaching refrains and using a microphone.',
						'<strong>Your own practice.</strong> A 20-minute daily sadhana, choosing mantras, caring for your harmonium and planning what to learn next.',
					),
					true
				)
				. kms_sc_h( 'Learning from the USA, UK, Europe or Australia' )
				. kms_sc_p( 'Classes run between 9 AM and 9 PM India time, 7 days a week. Students in Europe usually take morning or afternoon classes, students in the Americas early-morning or late-evening classes, and students in Australia afternoon or evening classes. The schedule box on this page shows the hours in your own time zone.' ),
		),
		array(
			'slug'    => 'singing',
			'title'   => 'Online Indian Singing Classes',
			'excerpt' => 'Live one-to-one Indian singing lessons rooted in Hindustani classical training: from Sa Re Ga Ma to ragas, bhajans and kirtan.',
			'order'   => 2,
			'meta'    => array(
				'kms_icon'         => 'mic-vocal',
				'kms_tagline'      => 'From Sa Re Ga Ma to ragas and bhajans: build pitch, breath and confidence.',
				'kms_answer'       => 'Live one-to-one Indian singing lessons on {platforms} with {founder}, rooted in Hindustani classical training. Ideal if you believe you cannot sing, and just as useful for experienced singers who want raga, bhajan and kirtan technique. Each online class lasts {minutes} minutes; a single class costs {price_single} and 10 classes cost {price_pack10}.',
				'kms_audience'     => "\"I can't sing\" beginners — we start from your natural voice\nYoga teachers and kirtan leaders who want a steadier, stronger voice\nSingers from other styles (pop, jazz, choir) exploring Indian music\nSpiritual seekers who want to chant mantras correctly",
				'kms_level'        => 'Complete beginner to advanced',
				'kms_outcomes'     => "Find your natural Sa and sing in tune with a tanpura drone\nBreathe from the diaphragm and sing without strain\nPractise alankars and paltas that build accuracy and agility\nUnderstand what a raga is and sing Raga Bhupali and Raga Yaman\nSing 3–5 bhajans and kirtans with meaning and feeling (bhava)\nKeep time in Teentaal and Keharwa while you sing",
				'kms_requirements' => "Just your voice — no instrument is needed to start\nA laptop or tablet with Zoom or Google Meet, a stable connection and headphones\nA quiet space where you can sing freely",
				'kms_modes'        => 'online,inperson',
				'kms_price_single' => 1500,
				'kms_price_pack5'  => 6500,
				'kms_price_pack10' => 13500,
				'kms_teaches'      => 'Indian vocal technique, Swaras and sargam, Breath control, Alankars and paltas, Raga basics, Bhajan singing, Kirtan singing, Mantra chanting',
				'kms_wa_text'      => 'Hi! I would like to take Indian singing classes online. Can we book the free consultation?',
			),
			'content' => kms_sc_h( 'What you learn, class by class' )
				. kms_sc_h( 'Foundation: classes 1–5', 3 )
				. kms_sc_list(
					array(
						'<strong>The Indian swaras.</strong> Sa Re Ga Ma Pa Dha Ni Sa with a tanpura drone; natural (shuddh), flat (komal) and sharp (tivra) notes; the three octaves; holding a steady Sa.',
						'<strong>Voice culture and breath.</strong> Diaphragmatic breathing, pranayama for singers (anulom vilom, bhastrika), warm-ups that protect the voice, and posture.',
						'<strong>Alankars and paltas.</strong> The classic patterns that train accuracy and flexibility, and a 15–20 minute daily routine.',
						'<strong>What a raga is.</strong> Note selection, mood and time of day, the most important notes (vadi and samvadi), and your first raga, Bhupali.',
						'<strong>Your first bhajan.</strong> Raghupati Raghav Raja Ram or Om Namah Shivaya, with its meaning, melody, rhythm and feeling (bhava).',
					),
					true
				)
				. kms_sc_h( 'Intermediate: classes 6–10', 3 )
				. kms_sc_list(
					array(
						'<strong>Raga Yaman.</strong> Its structure, slow alaap, simple taans and a short bandish (composition).',
						'<strong>Tala for singers.</strong> Teentaal (16 beats) and Keharwa (8 beats), and singing a bhajan while clapping the tala.',
						'<strong>Kirtan technique.</strong> Call and response, building intensity, repeating a phrase without strain, and basic harmonium accompaniment.',
						'<strong>Mantra chanting.</strong> Sanskrit pronunciation and breath-aligned chanting: Gayatri Mantra, Mahamrityunjaya Mantra and Om Namah Shivaya.',
						'<strong>Performance.</strong> Prepare two or three pieces, record yourself, and leave with a personal plan for what to learn next.',
					),
					true
				)
				. kms_sc_h( 'How a 40-minute online class runs' )
				. kms_sc_list(
					array(
						'Warm-up and breathing exercises to prepare your voice safely.',
						'Core teaching: raga, alankar or bhajan, depending on your level and goals.',
						'Practice together with personal feedback, then homework for the week.',
						'Afterwards: the recording of the class and any notation or lyrics as a PDF.',
					)
				),
		),
		array(
			'slug'    => 'bhajan-kirtan',
			'title'   => 'Online Bhajan & Kirtan Classes',
			'excerpt' => 'Learn to sing and lead devotional music: traditional bhajans, mantras and call-and-response kirtan, with simple harmonium accompaniment.',
			'order'   => 3,
			'meta'    => array(
				'kms_icon'         => 'heart-handshake',
				'kms_tagline'      => 'Sing and lead devotional music: mantras, bhajans and call-and-response kirtan.',
				'kms_answer'       => 'Live one-to-one bhajan and kirtan classes on {platforms} with {founder}, for yoga teachers, kirtan leaders and spiritual seekers. You learn traditional bhajans and mantras, how to lead call-and-response kirtan, correct Sanskrit pronunciation and simple harmonium accompaniment. The 5-class kirtan track costs {price_pack5}; single classes cost {price_single}.',
				'kms_audience'     => "Yoga teachers who want to bring live chanting into class\nNew and experienced kirtan leaders\nSpiritual seekers and meditators\nComplete beginners — no music experience needed",
				'kms_level'        => 'Complete beginner to intermediate',
				'kms_outcomes'     => "Sing traditional bhajans and mantras such as the Hare Krishna maha mantra and Om Namah Shivaya\nLead call-and-response kirtan and build its energy from slow to ecstatic\nPronounce Sanskrit vowels and consonants correctly\nAccompany yourself with simple harmonium chords\nKeep rhythm with clapping and manjira (small cymbals)\nUnderstand the meaning and tradition behind what you sing",
				'kms_requirements' => "Your voice; a harmonium helps but is not needed for the first classes\nZoom or Google Meet on a laptop or tablet, and a stable connection",
				'kms_modes'        => 'online,inperson',
				'kms_price_single' => 1500,
				'kms_price_pack5'  => 6500,
				'kms_price_pack10' => 0,
				'kms_teaches'      => 'Bhajan singing, Kirtan leading, Call and response, Mantra pronunciation, Harmonium accompaniment, Devotional rhythm',
				'kms_wa_text'      => 'Hi! I would like to learn bhajan and kirtan online. Can we book the free consultation?',
			),
			'content' => kms_sc_h( 'Bhajan and kirtan: what is the difference?' )
				. kms_sc_p( 'A <strong>bhajan</strong> is a devotional song, sung solo or in a small group. It is meditative and melodic and often follows a classical raga. <strong>Kirtan</strong> is communal call-and-response chanting: one person leads a mantra and the group repeats it. It is energetic and participatory, and central to bhakti yoga, the yoga of devotion.' )
				. kms_sc_h( 'Choose your 5-class track' )
				. kms_sc_h( 'Track A: kirtan leader foundations', 3 )
				. kms_sc_list(
					array(
						'Call-and-response structure and the traditional arc of a kirtan, from slow and meditative to ecstatic.',
						'Three or four simple kirtans: Om Namah Shivaya, Hare Krishna and Govinda Jaya Jaya (classes 2–3).',
						'Harmonium chords so you can accompany yourself.',
						'Lead a practice kirtan with your teacher as the chorus.',
					),
					true
				)
				. kms_sc_h( 'Track B: bhajan singing', 3 )
				. kms_sc_list(
					array(
						'Two popular bhajans sung with devotional expression, such as Raghupati Raghav and Om Namah Shivaya (classes 1–2).',
						'Harmonium basics for self-accompaniment.',
						'Singing with the rhythm of tabla or dholak.',
						'Performance practice and a recording, with detailed feedback.',
					),
					true
				),
		),
		array(
			'slug'    => 'indian-classical-vocal',
			'title'   => 'Online Indian Classical Vocal Classes (Raga & Khayal)',
			'excerpt' => 'Structured Hindustani classical vocal training: swar sadhana, ragas, alaap, bandish and taan, taught one-to-one in the guru–shishya way.',
			'order'   => 4,
			'meta'    => array(
				'kms_icon'         => 'list-music',
				'kms_tagline'      => 'Structured Hindustani classical training: swar sadhana, ragas, alaap, bandish and taan.',
				'kms_answer'       => 'Long-term, one-to-one Hindustani classical vocal training on {platforms} with {founder}: from voice culture and your first ragas to khayal with alaap, bandish and taan. Taught in the traditional guru–shishya way and adapted for students anywhere in the world. Classes cost {price_single} each; long-term programmes are planned with you.',
				'kms_audience'     => "Serious beginners ready for regular riyaaz (daily practice)\nBhajan and kirtan singers who want classical depth\nMusicians from other traditions — jazz, Western classical, world music\nStudents of ethnomusicology who want to learn by doing",
				'kms_level'        => 'Beginner to advanced (long-term)',
				'kms_outcomes'     => "Sing sargam, alankars and paltas with accurate pitch\nLearn 3–5 foundation ragas such as Yaman, Bhairav, Bhupali, Kafi and Bageshri\nUnderstand thaat, aroha–avaroha, vadi–samvadi and pakad\nSing simple bandishes in Teentaal, Ektaal and Jhaptaal\nBuild a daily riyaaz routine that keeps you improving",
				'kms_requirements' => "Your voice and a tanpura drone (a free tanpura app is enough)\nZoom or Google Meet, a stable connection and headphones\nTime for daily practice",
				'kms_modes'        => 'online,inperson',
				'kms_price_single' => 1500,
				'kms_price_pack5'  => 0,
				'kms_price_pack10' => 0,
				'kms_price_note'   => 'Long-term programmes are planned and priced with you.',
				'kms_teaches'      => 'Hindustani classical vocal, Swar sadhana, Alankars, Raga, Alaap, Bandish, Taan, Sargam, Khayal',
				'kms_wa_text'      => 'Hi! I am interested in Hindustani classical vocal classes online. Can we book the free consultation?',
			),
			'content' => kms_sc_h( 'What are raga and khayal?' )
				. kms_sc_p( 'A <strong>raga</strong> is a melodic framework, not just a scale: it has rules for ascent and descent, notes to emphasise, and a mood. Many ragas belong to a time of day, such as Bhairav in the morning and Yaman in the evening. <strong>Khayal</strong> (“imagination”) is the main form of Hindustani classical singing. It combines a fixed composition (bandish) with improvisation: slow alaap, bol-bant (rhythmic play with the words), sargam, fast taans and layakari (rhythmic play with the tabla).' )
				. kms_sc_h( 'Level 1: foundation (about 3–6 months)' )
				. kms_sc_list(
					array(
						'<strong>Voice training and swar sadhana:</strong> breathing for singers, finding your natural Sa, sargam, alankars and pitch accuracy.',
						'<strong>First ragas:</strong> Yaman, Bhairav, Bhupali, Kafi and Bageshri.',
						'<strong>Theory:</strong> thaat (parent scales), aroha–avaroha, vadi–samvadi, pakad and the time theory of ragas.',
						'<strong>Compositions and tala:</strong> simple bandishes and the common talas Teentaal (16 beats), Ektaal (12) and Jhaptaal (10).',
					)
				)
				. kms_sc_h( 'Level 2: intermediate (6 months to 2 years)' )
				. kms_sc_list(
					array(
						'<strong>10–15 more ragas</strong> across the day, for example Ahir Bhairav, Todi and Lalit (morning), Multani and Patdeep (afternoon), Puriya Dhanashree, Marwa and Shree (evening), Malkauns, Darbari Kanada and Jaunpuri (night).',
						'<strong>Improvisation:</strong> alaap phrases, taan types (sapat, koot, firat, gamak), sargam and bol-bant.',
						'<strong>Vilambit and drut khayal</strong> in each raga, and singing with tabla.',
						'<strong>Performance:</strong> building a 30–45 minute recital.',
					)
				)
				. kms_sc_h( 'Level 3: advanced (2+ years)' )
				. kms_sc_p( 'Rarer and more complex ragas, extended alaap, a personal taan vocabulary and concert-length performance.' ),
		),
		array(
			'slug'    => 'tabla',
			'title'   => 'Online Tabla Classes',
			'excerpt' => 'Live one-to-one tabla lessons with a teacher whose main instrument is tabla: technique, bols, thekas and your first compositions.',
			'order'   => 5,
			'meta'    => array(
				'kms_icon'         => 'drum',
				'kms_tagline'      => 'Correct technique from the start: bols, thekas and your first compositions.',
				'kms_answer'       => 'Live one-to-one tabla lessons on {platforms} with {founder}, whose main instrument is tabla; most of the school\'s TripAdvisor reviews come from tabla students. You start with hand position and the basic bols, then learn thekas such as Teentaal and Keharwa and your first compositions. Each online class lasts {minutes} minutes; a single class costs {price_single}.',
				'kms_audience'     => "Complete beginners\nBhajan and kirtan musicians who want to keep rhythm\nDrummers (djembe, drum kit) learning Indian rhythm\nFormer students continuing what they started in Pushkar",
				'kms_level'        => 'Complete beginner to intermediate',
				'kms_outcomes'     => "Sit correctly and place your hands on the dayan and bayan\nPlay the basic bols cleanly: Na, Ta, Tin, Te, Ge, Ke, Dha, Dhin\nPlay the thekas of Keharwa, Dadra and Teentaal\nUnderstand matra, tala, sam and khali\nPlay simple kaidas and tihais\nAccompany a simple bhajan",
				'kms_requirements' => "A pair of tabla (dayan and bayan) with rings — we can advise you on choosing a set\nA camera angle that shows both hands clearly\nZoom or Google Meet and a stable connection",
				'kms_modes'        => 'online,inperson',
				'kms_price_single' => 1500,
				'kms_price_pack5'  => 6500,
				'kms_price_pack10' => 13500,
				'kms_teaches'      => 'Tabla technique, Bols, Theka, Teentaal, Keharwa, Dadra, Kaida, Tihai, Laya',
				'kms_wa_text'      => 'Hi! I would like to learn tabla online. Can we book the free consultation?',
			),
			'content' => kms_sc_h( 'How the tabla classes are structured' )
				. kms_sc_h( 'First steps', 3 )
				. kms_sc_list(
					array(
						'Sitting, hand position and the sound of each drum.',
						'The basic bols: Na, Ta, Tin, Te, Ge, Ke, Dha and Dhin.',
						'Your first theka: Keharwa (8 beats) or Dadra (6 beats).',
					)
				)
				. kms_sc_h( 'Building rhythm', 3 )
				. kms_sc_list(
					array(
						'Teentaal (16 beats): theka, sam and khali.',
						'Counting and clapping the tala and keeping a steady tempo (laya).',
						'Simple kaidas and tihais.',
					)
				)
				. kms_sc_h( 'Playing with others', 3 )
				. kms_sc_list(
					array(
						'Accompanying a bhajan or kirtan.',
						'Speed and clarity exercises for daily riyaaz.',
					)
				)
				. kms_sc_h( 'Does tabla work online?' )
				. kms_sc_p( 'Yes, with a camera angle that shows both hands. Between classes you record your practice and send it on WhatsApp for feedback, so mistakes are corrected before they become habits.' ),
		),
	);
}

/**
 * FAQ groups: slug => name.
 *
 * @return array<string, string>
 */
function kms_starter_faq_groups() {
	return array(
		'general'                => 'Online classes (general)',
		'pricing'                => 'Prices and payment',
		'retreats'               => 'Retreats',
		'performances'           => 'Live performances',
		'harmonium'              => 'Class: Harmonium',
		'singing'                => 'Class: Singing',
		'bhajan-kirtan'          => 'Class: Bhajan & Kirtan',
		'indian-classical-vocal' => 'Class: Indian Classical Vocal',
		'tabla'                  => 'Class: Tabla',
	);
}

/**
 * FAQs: [ group, question, answer ].
 *
 * @return array<int, array{0:string,1:string,2:string}>
 */
function kms_starter_faqs() {
	return array(
		array( 'general', 'Can I learn Indian music online as a complete beginner?', 'Yes. Most of our students start from zero. The first classes cover the basics — how to sit, how to find Sa (your home note), how to breathe and, for harmonium, how to pump the bellows — and you progress at your own pace in a patient, judgment-free class.' ),
		array( 'general', 'What do I need for online classes?', 'A laptop or tablet with {platforms}, a stable internet connection, a quiet space and headphones. For singing you only need your voice. For harmonium or tabla classes you need the instrument; we can advise you on buying or renting one.' ),
		array( 'general', 'How long is each online class?', 'Online classes last {minutes} minutes and are one-to-one with your teacher. In-person classes in Pushkar last {inperson_minutes} minutes.' ),
		array( 'general', 'Which time zones do you teach?', 'All of them. Classes run between 9 AM and 9 PM India time (IST), 7 days a week. Students in Europe usually take morning or afternoon classes, students in the Americas early-morning or late-evening classes, and students in Australia afternoon or evening classes. Each class page shows these hours in your own time zone.' ),
		array( 'general', 'Who teaches the classes?', '{founder}, the founder of the school, teaches most classes personally. {founder} has taught since {founding_year}, comes from three generations of musicians and holds a degree in Indian Classical Vocal Music from Government College, Ajmer.' ),
		array( 'general', 'Will I get recordings and notes?', 'Yes. You receive a recording of each online class to practise with, notation or lyrics as PDFs, and WhatsApp support between classes.' ),
		array( 'general', 'How much should I practise?', '15–20 minutes a day is enough for steady progress, and even 10 minutes helps. Consistency matters more than duration.' ),
		array( 'general', 'Do you teach children?', 'Yes, from age {min_age}. Younger children can learn with a parent taking part in the class.' ),
		array( 'general', 'Do I get a certificate?', 'Yes. 10-class packages and in-person immersion courses in Pushkar include a certificate of completion that lists your training hours and the topics covered.' ),
		array( 'general', 'Can I combine online classes with lessons in Pushkar?', 'Yes. Packages can be used online, in person in Pushkar, or a mix of both — for example, start online and continue at the school when you visit India.' ),

		array( 'pricing', 'How much do online classes cost?', 'A single online class starts at {single_from}, and packages bring the price down to {price_from} per class. Every class page shows its prices in Indian rupees with an approximate conversion to US dollars, euros or pounds.' ),
		array( 'pricing', 'How do I pay?', '{payment_methods}. Prices are set in Indian rupees; if you pay from abroad, PayPal or Wise converts the amount.' ),
		array( 'pricing', 'What if I do not enjoy my first class?', '{guarantee}' ),
		array( 'pricing', 'Can I reschedule a class?', '{reschedule}' ),
		array( 'pricing', 'How long are packages valid?', '{validity} Unused classes can be gifted to a friend or family member.' ),

		array( 'retreats', 'Are accommodation and meals included in the retreats?', 'No. The retreat price covers the teaching. We can recommend guesthouses within walking distance of the school.' ),
		array( 'retreats', 'How big are the retreat groups?', 'A maximum of 10 students per batch, so everyone gets personal guidance.' ),
		array( 'retreats', 'Do I need musical experience to join a retreat?', 'No. The retreats are designed for beginners, music lovers and spiritual seekers, and the teaching adapts to each student.' ),

		array( 'performances', 'How do I book a live performance?', 'Send a WhatsApp message to {phone} with your event date, venue, audience size and type of event. We suggest artists and send a quote within 24 hours. Bookings are confirmed with a 50% advance, and the balance is paid after the event.' ),

		array( 'harmonium', 'Do I need my own harmonium?', 'Yes, for online classes you need a harmonium at home. A good student instrument costs roughly ₹3,000–8,000 in India, and we can advise you on buying or renting one.' ),
		array( 'harmonium', 'How quickly will I play my first bhajan?', 'Most students play simple bhajans within the first few classes and 4–5 bhajans or kirtans confidently within 10 classes, practising 15–20 minutes a day.' ),
		array( 'harmonium', 'I am a yoga teacher. Is this the right class?', 'Yes. The course was designed with yoga teachers and kirtan leaders in mind: you learn to accompany yourself and lead simple chants and kirtans in your own classes.' ),

		array( 'singing', 'I think I am tone-deaf. Can I still learn?', 'Yes. Pitch is a skill that can be trained: we start with one steady note (Sa) against a drone and build from there. Many students arrive convinced they cannot sing.' ),
		array( 'singing', 'Is this Hindustani or Carnatic singing?', 'Hindustani (North Indian) classical singing, together with devotional bhajan and kirtan. We do not teach Carnatic (South Indian) music.' ),
		array( 'singing', 'Do I need to read music?', 'No. Indian music is taught by ear. We use sargam (Sa Re Ga Ma) and lyric sheets with transliteration.' ),

		array( 'bhajan-kirtan', 'Do I need to know Sanskrit or Hindi?', 'No. Lyrics come with transliteration and meaning, and pronunciation is taught step by step.' ),
		array( 'bhajan-kirtan', 'Will I be able to lead kirtan after the 5-class track?', 'Yes — simple kirtans with call and response and basic harmonium accompaniment, which is enough to lead chanting in a yoga class or a small group.' ),

		array( 'indian-classical-vocal', 'How long does it take to learn a raga?', 'At the foundation level, with regular classes and daily riyaaz, students usually learn 3–5 ragas in about 3–6 months. Classical music is a long-term practice, so the programme is planned with you.' ),
		array( 'indian-classical-vocal', 'I already sing. Where would I start?', 'Your first class includes an assessment of your voice and experience, so you start at the right level rather than from the beginning.' ),

		array( 'tabla', 'Do I need my own tabla?', 'Yes, you need a pair (dayan and bayan) at home. We can advise you on choosing a set; students visiting Pushkar have bought their tabla with the school\'s help.' ),
		array( 'tabla', 'Can tabla really be learned online?', 'Yes, with a camera angle that shows both hands. Between classes you send short practice videos on WhatsApp for feedback, so mistakes are corrected early.' ),
	);
}

/**
 * Reviews already published on the site and attributed to TripAdvisor.
 * Imported unverified: tick "Verified" after matching each on TripAdvisor.
 *
 * @return array<int, array<string, mixed>>
 */
function kms_starter_reviews() {
	return array(
		array(
			'name'    => 'Annie Prashant',
			'loc'     => 'Wales, UK',
			'date'    => '2019-12',
			'subject' => 'Raga singing, 2 weeks (twice daily)',
			'course'  => 'indian-classical-vocal',
			'text'    => 'I have just spent two weeks learning the rudiments of Raga singing with Vini. We have had twice daily sessions of one hour each. I have to leave today but I am carrying with me both a restored sense of my connection to pure sound and a darling little travellers harmonium. Pushkar is a dream town with many hidden treasures. The Krishna music school is one of them. If your instincts guide your feet to this place, trust.',
		),
		array(
			'name'    => 'Fanny O.',
			'loc'     => 'Paris, France',
			'date'    => '2020-01',
			'subject' => 'Singing class, 1 hour',
			'course'  => 'singing',
			'text'    => 'I was in Pushkar for less than 2 days but went for one hour singing class. Vini managed in an hour to give me lots of tips and opened a different perception of the voice. Very patient, kind and enthusiastic! I am going to plan and organise myself to come back for 3 weeks for more classes! Do not hesitate this is a very nice experience and totally worthwhile!',
		),
		array(
			'name'    => 'Chris',
			'loc'     => 'Glasgow, UK',
			'date'    => '2014-12',
			'subject' => 'Daily tabla lessons, 1 week',
			'course'  => 'tabla',
			'text'    => 'Excellent tabla lessons, the best I\'ve had so far in India. I\'ve had daily lessons with Vini for the week I\'ve been in Pushkar. He\'s great at teaching proper technique and good habits, he has a quality sense of humour and a fantastic attitude. Central location, excellent surroundings, great experience!',
		),
		array(
			'name'    => 'Hummingbird',
			'loc'     => 'Seattle, USA',
			'date'    => '2020-02',
			'subject' => 'Beginning sitar lessons',
			'course'  => '',
			'text'    => 'I had the pleasure of studying beginning sitar with owner Vini Devda who is a patient teacher and masterful musician. Individual lessons were easy to arrange and affordable. I would highly recommend this school if you are looking for lessons in classical Indian music or dance. Plus, on very short notice, Vini used his contacts to help me purchase my very own sitar. Couldn\'t ask for more or better.',
		),
		array(
			'name'    => 'Tatiana Gogol',
			'loc'     => 'Bali, Indonesia',
			'date'    => '2015-04',
			'subject' => 'Vocal classes, 3 weeks daily',
			'course'  => 'singing',
			'text'    => 'Excellent! Great friendship. It was my first experience with vocal classes! And it was awesome. Vini is super fun, but focused. I was very enjoying my every day classes for 3 weeks. My voice opened up and now I have a solid practice I do myself! Thank you Vini for being a patient teacher and a great friend. Blessings!',
		),
		array(
			'name'    => 'Faith C.',
			'loc'     => 'USA',
			'date'    => '',
			'subject' => 'Indian vocal music, 1-week intensive',
			'course'  => 'singing',
			'text'    => 'Vini is a very dedicated teacher who I enjoyed learning from when I was in Pushkar. I only had one week, so he recommended two lessons a day for a deeper engagement in the basic techniques of Indian vocal music. His method for teaching was methodical and each day built on the last day. I was given his undivided attention for 1 hour each lesson and my voice improved with each lesson. An unexpected bonus: seeing that I had had some musical background already, and loved singing the Hanuman Chalisa, he proposed doing a recording with me. It was an honor, and I was amazed at how wonderfully he added his own vocal line to accompany mine. It was so much fun! His heart is in his teaching and in his music. I recommend this little school for anyone interested in learning one-on-one with a great teacher.',
		),
		array(
			'name'    => 'Elle A.',
			'loc'     => 'London, UK',
			'date'    => '',
			'subject' => 'Tabla lessons, several weeks',
			'course'  => 'tabla',
			'text'    => 'I decided to learn the tabla with Vini on my arrival to Pushkar, as a complete beginner I was nervous but I ended up loving it so much that I adapted my travel plans so that I could return to Pushkar to continue with my lessons. Vini is an excellent teacher, he is fun, enthusiastic and focused and he makes you feel comfortable and at ease. He is professional and patient. He is also an extremely accomplished musician, you must ask him to sing for you or to play you one of his wild drum solos. I was so impressed with his skill and his teaching ability that I am planning to arrange for him to visit my school in London to carry out music workshops with my pupils. He arranged for me to purchase my own set of tabla and I am having them sent home to me in the UK for a very reasonable price. (If only I could bring my favourite teacher back with me too!)',
		),
		array(
			'name'    => 'Tracy H.',
			'loc'     => '',
			'date'    => '2017-10',
			'subject' => 'Tabla lessons, 6 days',
			'course'  => 'tabla',
			'text'    => 'Fantastic tabla lessons! I had several interesting and enjoyable tabla lessons over the course of six days. Vini is a great guy, musician and teacher. He has a very friendly, relaxed and enthusiastic manner that puts you at ease straight away. I am looking forward to continuing my tabla studies as I travel through India. Thank you Vini!',
		),
		array(
			'name'    => 'Gir Dhar',
			'loc'     => '',
			'date'    => '2014-12',
			'subject' => 'Tabla lessons',
			'course'  => 'tabla',
			'text'    => 'Best tabla lessons I had in a long time. Vini is a great, loving and passionate teacher. Learn by the temple in a quiet and lovely atmosphere. I wish I could\'ve spent more time taking class with him.',
		),
		array(
			'name'    => 'Risto P.',
			'loc'     => 'Espoo, Finland',
			'date'    => '2014-11',
			'subject' => 'Tabla lessons',
			'course'  => 'tabla',
			'text'    => 'Highly recommended. I am taking tabla lessons from Vini and I can recommend him if anyone wants to learn secrets of this instrument. Location is peaceful and Vini is excellent teacher.',
		),
	);
}

/**
 * Pages: slug => [ title, content, replaceable ].
 *
 * "replaceable" pages already exist on the live site; their content is only
 * replaced when the editor ticks "Replace existing pages" (the old content is
 * kept in Revisions).
 *
 * @return array<string, array{0:string,1:string,2:bool}>
 */
function kms_starter_pages() {
	$retreat_terms = '/pushkar-retreat-guidelines-terms-of-participation/';

	return array(
		'pricing'                 => array(
			'Prices for Online Music Classes',
			kms_sc_p( 'Every online class is live and one-to-one with {founder}. Prices are in Indian rupees; choose your currency to see an approximate conversion. Packages lower the price per class.' )
			. kms_sc_code( '[kms_pricing]' )
			. kms_sc_h( 'Included with every class' )
			. kms_sc_code( '[kms_includes]' )
			. kms_sc_h( 'Payment, guarantee and rescheduling' )
			. kms_sc_code( '[kms_faqs group="pricing"]' )
			. kms_sc_h( 'Retreats in India' )
			. kms_sc_code( '[kms_retreats]' )
			. kms_sc_h( 'Not sure which class to choose?' )
			. kms_sc_p( 'Book a free 15-minute consultation and we will recommend the right class and format for your goals.' )
			. kms_sc_code( '[kms_button]' ),
			false,
		),
		'faq'                     => array(
			'Frequently Asked Questions',
			kms_sc_p( 'Quick answers about online classes, prices, retreats and live performances. Can’t find your question? {response_time}' )
			. kms_sc_h( 'Online classes' )
			. kms_sc_code( '[kms_faqs group="general"]' )
			. kms_sc_h( 'Prices and payment' )
			. kms_sc_code( '[kms_faqs group="pricing"]' )
			. kms_sc_h( 'Retreats in India' )
			. kms_sc_code( '[kms_faqs group="retreats"]' )
			. kms_sc_h( 'Live performances' )
			. kms_sc_code( '[kms_faqs group="performances"]' )
			. kms_sc_code( '[kms_button]' ),
			false,
		),
		'refund-policy'           => array(
			'Refund and Cancellation Policy',
			kms_sc_p( '<strong>[Draft for the school to confirm — delete this line and every bracketed note before publishing.]</strong>' )
			. kms_sc_h( 'Online and in-person classes' )
			. kms_sc_h( 'Your first class', 3 )
			. kms_sc_p( '{guarantee}' )
			. kms_sc_h( 'Rescheduling', 3 )
			. kms_sc_p( '{reschedule}' )
			. kms_sc_h( 'Late cancellations and missed classes', 3 )
			. kms_sc_p( '[To confirm: is a class cancelled less than 4 hours before, or missed, counted as taken?]' )
			. kms_sc_h( 'Packages', 3 )
			. kms_sc_p( '{validity} Unused classes can be gifted to a friend or family member. [To confirm: can unused classes be refunded, and how?]' )
			. kms_sc_h( 'Retreats' )
			. kms_sc_p( 'Deposits and cancellations for retreats follow the <a href="' . $retreat_terms . '">retreat guidelines and terms of participation</a>.' )
			. kms_sc_h( 'Live performance bookings' )
			. kms_sc_p( 'Bookings are confirmed with a 50% advance; the balance is paid after the event. [To confirm: cancellation terms for performances.]' )
			. kms_sc_h( 'How to request a refund or a change' )
			. kms_sc_p( 'Message us on WhatsApp at {phone} or email {email} with your name and the class or booking concerned.' ),
			false,
		),
		'about-us'                => array(
			'About Krishna Music School',
			kms_sc_p( '{name} is an Indian music school in Pushkar, Rajasthan, founded in {founding_year} by {founder}. We teach harmonium, Indian singing, bhajan and kirtan, Hindustani classical vocal and tabla — live and one-to-one, online anywhere in the world or in person at the historic Rangji Temple.' )
			. kms_sc_code( '[kms_founder heading="h2"]' )
			. kms_sc_h( 'Krishna Music School at a glance' )
			. kms_sc_code( '[kms_facts]' )
			. kms_sc_h( 'Our story' )
			. kms_sc_h( '2008: the beginning', 3 )
			. kms_sc_p( '{founder} founded the school in Pushkar with a vision: to make authentic Indian music accessible to learners from around the world while keeping the traditional way of teaching. It started with one-to-one tabla lessons for travellers visiting Pushkar, and grew by word of mouth.' )
			. kms_sc_h( '2010–2015: more instruments and a band', 3 )
			. kms_sc_p( 'Teaching expanded from tabla to harmonium, sitar, vocals, dholak and percussion, and students began arriving from all over the world for serious training. In 2015 the school founded the {band}, its performing Rajasthani folk band, which plays at hotels, weddings and cultural events.' )
			. kms_sc_h( '2018: online classes', 3 )
			. kms_sc_p( 'Online classes began for students who could not travel to Pushkar, with a structured curriculum, recordings and practice feedback on WhatsApp — keeping the personal attention of the gurukul way of teaching.' )
			. kms_sc_h( 'Today', 3 )
			. kms_sc_p( 'Students from {countries} countries learn with us online and in Pushkar, and the school runs music retreats in Pushkar in winter and in the Himalayas (Upper Bhagsu) in summer.' )
			. kms_sc_h( 'Why “Krishna”?' )
			. kms_sc_p( 'The school is named after Lord Krishna, the divine flute player and an embodiment of musical devotion. For us, music is not only performance: it is a spiritual practice, a form of meditation and a way to connect with the divine.' )
			. kms_sc_h( 'How we teach' )
			. kms_sc_list(
				array(
					'<strong>Assessment and goals.</strong> A free 15-minute consultation to understand your background, goals and schedule, and to recommend the right format.',
					'<strong>Foundation first.</strong> Breath and sargam for singers, hand position and bols for tabla, fingering and drone for harmonium. No shortcuts: a strong foundation means faster progress later.',
					'<strong>Structured practice.</strong> Each lesson builds on the last, with homework and clear practice goals. You can record your practice and send it on WhatsApp for feedback.',
					'<strong>Cultural context.</strong> The story behind each raga, the meaning of each bhajan, and when and where a composition is traditionally performed.',
					'<strong>Performance.</strong> At the end of a course you perform what you have learned; we record it, and 10-class packages include a certificate.',
				),
				true
			)
			. kms_sc_h( 'Where we teach' )
			. kms_sc_p( 'In-person classes take place at the historic Rangji Temple in Pushkar — a quiet place away from the busy bazaar, within walking distance of Pushkar Lake, with temple bells in the background of the morning classes.' )
			. kms_sc_code( '[kms_contact]' )
			. kms_sc_h( 'What students say' )
			. kms_sc_code( '[kms_reviews limit="3"]' )
			. kms_sc_code( '[kms_button]' ),
			true,
		),
		'hear-from-our-attendees' => array(
			'Student Reviews',
			kms_sc_p( 'Every review on this page was written by a student on TripAdvisor or Google, and each one links to the platform where it was published. Most are from students who studied with {founder} in Pushkar; online students are invited to add theirs.' )
			. kms_sc_code( '[kms_trust]' )
			. kms_sc_code( '[kms_reviews limit="0"]' )
			. kms_sc_h( 'Studied with us?' )
			. kms_sc_p( 'Your review helps other students find the right teacher. Thank you for taking a minute to write one.' )
			. kms_sc_code( '[kms_review_links]' )
			. kms_sc_code( '[kms_button]' ),
			true,
		),
		'contact-us'              => array(
			'Contact Us and Book a Free Consultation',
			kms_sc_p( 'Tell us what you would like to learn and we will arrange a free 15-minute consultation. {response_time}' )
			. kms_sc_code( '[kms_enquiry_form]' )
			. kms_sc_h( 'Other ways to reach us' )
			. kms_sc_code( '[kms_contact]' )
			. kms_sc_h( 'Class hours in your time zone' )
			. kms_sc_code( '[kms_timezone]' )
			. kms_sc_h( 'Visit the school in Pushkar' )
			. kms_sc_p( '{directions}' ),
			true,
		),
		'blog'                    => array( 'Blog', '', false ),
	);
}
