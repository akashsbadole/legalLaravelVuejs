<?php

namespace Database\Seeders;

use App\Models\CrpcSection;
use Illuminate\Database\Seeder;

class CrpcSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crpcSections = [
            [
                'section_number' => 41,
                'title' => 'When police may arrest without warrant',
                'description' => 'Any police officer may without an order from a Magistrate and without a warrant, arrest any person.',
                'procedure' => 'Police officer must have reasonable suspicion that person has committed or is about to commit a cognizable offence.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'Arrest must be followed by production before Magistrate within 24 hours.',
                'jurisdiction_details' => 'Any police officer in uniform or with identification.',
            ],
            [
                'section_number' => 50,
                'title' => 'Person arrested to be informed of grounds of arrest and of right to bail',
                'description' => 'Every police officer or other person arresting any person without warrant shall forthwith communicate to him full particulars of the offence for which he is arrested or other grounds for such arrest.',
                'procedure' => 'Inform arrested person of grounds of arrest and right to bail. Allow communication with friends/relatives.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'Information must be given immediately upon arrest.',
                'jurisdiction_details' => 'All arresting officers.',
            ],
            [
                'section_number' => 57,
                'title' => 'Person arrested not to be detained more than twenty-four hours',
                'description' => 'No police officer shall detain in custody a person arrested without warrant for a longer period than under all the circumstances of the case is reasonable, and such period shall not, in the absence of a special order of a Magistrate under section 167, exceed twenty-four hours exclusive of the time necessary for the journey from the place of arrest to the Magistrate\'s Court.',
                'procedure' => 'Produce arrested person before Magistrate within 24 hours of arrest.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'Maximum 24 hours detention without Magistrate\'s order.',
                'jurisdiction_details' => 'Police custody limited to 24 hours.',
            ],
            [
                'section_number' => 91,
                'title' => 'Summons',
                'description' => 'Whenever a Magistrate issues a summons, he may, if he sees reason so to do, dispense with the personal attendance of the witness and permit him to appear by affidavit.',
                'procedure' => 'Issue summons for appearance before court. May dispense with personal attendance.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'Summons must specify reasonable time for appearance.',
                'jurisdiction_details' => 'Magistrates and Courts.',
            ],
            [
                'section_number' => 154,
                'title' => 'Information in cognizable cases',
                'description' => 'Every information relating to the commission of a cognizable offence, if given orally to an officer in charge of a police station, shall be reduced to writing by him or under his direction, and be read over to the informant; and every such information, whether given in writing or reduced to writing as aforesaid, shall be signed by the person giving it, and the substance thereof shall be entered in a book to be kept by such officer in such form as the State Government may prescribe in this behalf.',
                'procedure' => 'Record FIR, enter in daily diary, investigate cognizable offence.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'Immediate recording and investigation.',
                'jurisdiction_details' => 'Police station within local limits.',
            ],
            [
                'section_number' => 156,
                'title' => 'Police officer\'s power to investigate cognizable case',
                'description' => 'Any officer in charge of a police station may, without the order of a Magistrate, investigate any cognizable case which a Court having jurisdiction over the local area within the limits of such station would have power to inquire into or try under the provisions of Chapter XIII.',
                'procedure' => 'Investigate cognizable offences without Magistrate\'s order.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'Prompt investigation.',
                'jurisdiction_details' => 'Police station jurisdiction.',
            ],
            [
                'section_number' => 164,
                'title' => 'Recording of confessions and statements',
                'description' => 'Any Metropolitan Magistrate or Judicial Magistrate may, whether or not he has jurisdiction in the case, record any confession or statement made to him in the course of an investigation or at any time afterwards before the commencement of the inquiry or trial.',
                'procedure' => 'Record confessions and statements in presence of Magistrate.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'During investigation or before trial.',
                'jurisdiction_details' => 'Metropolitan or Judicial Magistrates.',
            ],
            [
                'section_number' => 167,
                'title' => 'Procedure when investigation cannot be completed in twenty-four hours',
                'description' => 'Whenever any person is arrested and detained in custody, and it appears that the investigation cannot be completed within the period of twenty-four hours fixed by section 57, and there are grounds for believing that the accusation or information is well-founded.',
                'procedure' => 'Extend police custody beyond 24 hours with Magistrate\'s order.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'Maximum 15 days for police custody in serious cases.',
                'jurisdiction_details' => 'Magistrate having jurisdiction.',
            ],
            [
                'section_number' => 173,
                'title' => 'Report of police officer on completion of investigation',
                'description' => 'Every investigation under this Chapter shall be completed without unnecessary delay, and, as soon as it is completed, the officer in charge of the police station shall forward to a Magistrate empowered to take cognizance of the offence on a police-report, a report in the form prescribed by the State Government, setting forth the names of the parties, the nature of the information and the names of the persons who appear to be acquainted with the circumstances of the case.',
                'procedure' => 'File charge-sheet or final report after investigation.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'Complete investigation without unnecessary delay.',
                'jurisdiction_details' => 'Magistrate with cognizance power.',
            ],
            [
                'section_number' => 197,
                'title' => 'Prosecution of Judges and public servants',
                'description' => 'When any person who is or was a Judge or Magistrate or a public servant not removable from his office save by or with the sanction of the Government is accused of any offence alleged to have been committed by him while acting or purporting to act in the discharge of his official duty, no Court shall take cognizance of such offence except with the previous sanction.',
                'procedure' => 'Obtain sanction from Government before prosecuting public servants.',
                'court_level' => 'High Court',
                'timeline_requirements' => 'Sanction required before cognizance.',
                'jurisdiction_details' => 'Government sanction required.',
            ],
            [
                'section_number' => 239,
                'title' => 'When accused shall be discharged',
                'description' => 'If, upon considering the police report and the documents sent with it under section 173 and making such examination, if any, of the accused as the Magistrate thinks necessary and after giving the prosecution and the accused an opportunity of being heard, the Magistrate considers the charge against the accused to be groundless, he shall discharge the accused, and record his reasons for so doing.',
                'procedure' => 'Discharge accused if charge appears groundless.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'After considering police report and hearing parties.',
                'jurisdiction_details' => 'Magistrate trying the case.',
            ],
            [
                'section_number' => 313,
                'title' => 'Power to examine the accused',
                'description' => 'In every inquiry or trial, for the purpose of enabling the accused personally to explain any circumstances appearing in the evidence against him, the Court may at any stage, without previously warning the accused, put such questions to him as the Court considers necessary, and shall, for the purpose aforesaid, question him generally on the case after the witnesses for the prosecution have been examined and before he is called on for his defence.',
                'procedure' => 'Question accused after prosecution evidence, before defence.',
                'court_level' => 'Magistrate Court',
                'timeline_requirements' => 'After prosecution witnesses, before defence begins.',
                'jurisdiction_details' => 'Trial Courts.',
            ],
        ];

        foreach ($crpcSections as $section) {
            CrpcSection::updateOrCreate(
                ['section_number' => $section['section_number']],
                $section
            );
        }
    }
}
