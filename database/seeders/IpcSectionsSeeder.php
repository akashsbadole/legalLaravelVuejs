<?php

namespace Database\Seeders;

use App\Models\IpcSection;
use Illuminate\Database\Seeder;

class IpcSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ipcSections = [
            [
                'section_number' => 120,
                'title' => 'Concealing design to commit offence punishable with imprisonment',
                'description' => 'Whoever, intending to facilitate or knowing it to be likely that he will thereby facilitate the commission of an offence punishable with imprisonment, voluntarily conceals, by any act or illegal omission, the existence of a design to commit such offence, or makes any representation which he knows to be false respecting such design.',
                'classification' => 'bailable',
                'cognizable' => 'non-cognizable',
                'punishment_details' => 'Imprisonment up to 6 months, or fine, or both',
            ],
            [
                'section_number' => 302,
                'title' => 'Punishment for murder',
                'description' => 'Whoever commits murder shall be punished with death, or imprisonment for life, and shall also be liable to fine.',
                'classification' => 'non-bailable',
                'cognizable' => 'cognizable',
                'punishment_details' => 'Death penalty, or imprisonment for life, and fine',
            ],
            [
                'section_number' => 307,
                'title' => 'Attempt to murder',
                'description' => 'Whoever does any act with such intention or knowledge, and under such circumstances that, if he by that act caused death, he would be guilty of qatl-i-amd, shall be punished with imprisonment for life, or imprisonment of either description for a term which may extend to ten years, and shall also be liable to fine.',
                'classification' => 'non-bailable',
                'cognizable' => 'cognizable',
                'punishment_details' => 'Imprisonment for life, or up to 10 years, and fine',
            ],
            [
                'section_number' => 324,
                'title' => 'Attempt to murder',
                'description' => 'Whoever does any act with such intention or knowledge, and under such circumstances that, if he by that act caused death, he would be guilty of qatl-i-amd, shall be punished with imprisonment for life, or imprisonment of either description for a term which may extend to ten years, and shall also be liable to fine.',
                'classification' => 'non-bailable',
                'cognizable' => 'cognizable',
                'punishment_details' => 'Imprisonment for life, or up to 10 years, and fine',
            ],
            [
                'section_number' => 326,
                'title' => 'Voluntarily causing grievous hurt by dangerous weapons or means',
                'description' => 'Whoever, except in the case provided for by section 335, voluntarily causes grievous hurt by means of any instrument for shooting, stabbing or cutting, or any instrument which, used as a weapon of offence, is likely to cause death, or by means of fire or any heated substance, or by means of any poison or any corrosive substance, or by means of any explosive substance, or by means of any substance which it is deleterious to the human body to inhale, to swallow, or to receive into the blood, or by means of any animal, shall be punished with imprisonment for life, or with imprisonment of either description for a term which may extend to ten years, and shall also be liable to fine.',
                'classification' => 'non-bailable',
                'cognizable' => 'cognizable',
                'punishment_details' => 'Imprisonment for life, or up to 10 years, and fine',
            ],
            [
                'section_number' => 354,
                'title' => 'Assault or criminal force to woman with intent to outrage her modesty',
                'description' => 'Whoever assaults or uses criminal force to any woman, intending to outrage or knowing it to be likely that he will thereby outrage her modesty, shall be punished with imprisonment of either description for a term which shall not be less than one year but which may extend to five years, and shall also be liable to fine.',
                'classification' => 'non-bailable',
                'cognizable' => 'cognizable',
                'punishment_details' => 'Imprisonment of 1-5 years, and fine',
            ],
            [
                'section_number' => 376,
                'title' => 'Punishment for rape',
                'description' => 'Whoever, except in the cases provided for by sub-section (2), commits rape shall be punished with imprisonment of either description for a term which shall not be less than seven years but which may be for life or for a term which may extend to ten years and shall also be liable to fine.',
                'classification' => 'non-bailable',
                'cognizable' => 'cognizable',
                'punishment_details' => 'Imprisonment of 7 years to life, and fine',
            ],
            [
                'section_number' => 378,
                'title' => 'Theft',
                'description' => 'Whoever, intending to take dishonestly any movable property out of the possession of any person without that person\'s consent, moves that property in order to such taking, is said to commit theft.',
                'classification' => 'bailable',
                'cognizable' => 'cognizable',
                'punishment_details' => 'Imprisonment up to 3 years, or fine, or both',
            ],
            [
                'section_number' => 379,
                'title' => 'Punishment for theft',
                'description' => 'Whoever commits theft shall be punished with imprisonment of either description for a term which may extend to three years, or with fine, or with both.',
                'classification' => 'bailable',
                'cognizable' => 'cognizable',
                'punishment_details' => 'Imprisonment up to 3 years, or fine, or both',
            ],
            [
                'section_number' => 406,
                'title' => 'Punishment for criminal breach of trust',
                'description' => 'Whoever commits criminal breach of trust shall be punished with imprisonment of either description for a term which may extend to three years, or with fine, or with both.',
                'classification' => 'non-bailable',
                'cognizable' => 'non-cognizable',
                'punishment_details' => 'Imprisonment up to 3 years, or fine, or both',
            ],
            [
                'section_number' => 420,
                'title' => 'Cheating and dishonestly inducing delivery of property',
                'description' => 'Whoever cheats and thereby dishonestly induces the person deceived to deliver any property to any person, or to make, alter or destroy the whole or any part of a valuable security, or anything which is signed or sealed, and which is capable of being converted into a valuable security, shall be punished with imprisonment of either description for a term which may extend to seven years, and shall also be liable to fine.',
                'classification' => 'non-bailable',
                'cognizable' => 'non-cognizable',
                'punishment_details' => 'Imprisonment up to 7 years, and fine',
            ],
            [
                'section_number' => 498,
                'title' => 'Enticing or taking away or detaining with criminal intent a married woman',
                'description' => 'Whoever takes or entices away any woman who is and whom he knows or has reason to believe to be the wife of any other man, from that man, or from any person having the care of her on behalf of that man, with intent that she may have illicit intercourse with any person, or conceals or detains with that intent any such woman, shall be punished with imprisonment of either description for a term which may extend to two years, or with fine, or with both.',
                'classification' => 'non-bailable',
                'cognizable' => 'non-cognizable',
                'punishment_details' => 'Imprisonment up to 2 years, or fine, or both',
            ],
            [
                'section_number' => 509,
                'title' => 'Word, gesture or act intended to insult the modesty of a woman',
                'description' => 'Whoever, intending to insult the modesty of any woman, utters any word, makes any sound or gesture, or exhibits any object, intending that such word or sound shall be heard, or that such gesture or object shall be seen, by such woman, or intrudes upon the privacy of such woman, shall be punished with simple imprisonment for a term which may extend to one year, or with fine, or with both.',
                'classification' => 'bailable',
                'cognizable' => 'cognizable',
                'punishment_details' => 'Imprisonment up to 1 year, or fine, or both',
            ],
        ];

        foreach ($ipcSections as $section) {
            IpcSection::updateOrCreate(
                ['section_number' => $section['section_number']],
                $section
            );
        }
    }
}
