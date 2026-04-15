<?php

namespace Database\Seeders;

use App\Models\CpcSection;
use Illuminate\Database\Seeder;

class CpcSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cpcSections = [
            [
                'section_number' => 9,
                'title' => 'Courts to try all civil suits unless barred',
                'description' => 'The Courts shall (subject to the provisions herein contained) have jurisdiction to try all suits of a civil nature excepting suits of which their cognizance is either expressly or impliedly barred.',
                'procedure' => 'Civil courts have jurisdiction over all civil matters unless specifically barred.',
                'court_level' => 'Civil Court',
                'timeline_requirements' => 'No specific timeline mentioned.',
                'appeal_provisions' => 'Subject to appellate jurisdiction.',
            ],
            [
                'section_number' => 14,
                'title' => 'Presumption as to foreign judgments',
                'description' => 'The Court shall presume, upon the production of any document purporting to be a certified copy of a foreign judgment, that such judgment was pronounced by a Court of competent jurisdiction, unless the contrary appears on the record.',
                'procedure' => 'Presume foreign judgments are valid unless proved otherwise.',
                'court_level' => 'Civil Court',
                'timeline_requirements' => 'No limitation period specified.',
                'appeal_provisions' => 'May be challenged under section 13.',
            ],
            [
                'section_number' => 26,
                'title' => 'Institution of suits',
                'description' => 'Every suit shall be instituted by the presentation of a plaint or in such other manner as may be prescribed.',
                'procedure' => 'Suit begins with filing of plaint in prescribed manner.',
                'court_level' => 'Civil Court',
                'timeline_requirements' => 'Within limitation period.',
                'appeal_provisions' => 'Subject to appellate remedies.',
            ],
            [
                'section_number' => 80,
                'title' => 'Notice',
                'description' => 'Where a suit is instituted against the Government or against a public officer in respect of any act purporting to be done by such public officer in his official capacity, the authority empowered to defend the suit on behalf of the Government or the public officer shall be named as defendant.',
                'procedure' => 'Two months notice required before suing Government.',
                'court_level' => 'Civil Court',
                'timeline_requirements' => '2 months notice before filing suit.',
            ],
            [
                'section_number' => 89,
                'title' => 'Settlement of disputes outside the Court',
                'description' => 'Where it appears to the Court that there exist elements of a settlement which may be acceptable to the parties, the Court shall formulate the terms of settlement and give them to the parties for their observations.',
                'procedure' => 'Court may refer parties to arbitration, conciliation, mediation.',
                'court_level' => 'Civil Court',
                'timeline_requirements' => 'At any stage of the suit.',
                'appeal_provisions' => 'Settlement binding on parties.',
            ],
            [
                'section_number' => 96,
                'title' => 'Appeal from original decree',
                'description' => 'Subject to the provisions of section 100, a decree may be appealed from by any party thereto to the Court authorised to hear appeals from the decisions of the Court passing the decree.',
                'procedure' => 'Appeal against final decree to higher court.',
                'court_level' => 'High Court',
                'timeline_requirements' => '30 days from decree (90 days with sufficient cause).',
                'appeal_provisions' => 'First appeal from original decrees.',
            ],
            [
                'section_number' => 100,
                'title' => 'Second appeal',
                'description' => 'An appeal shall lie to the High Court from every decree passed in appeal by any Court subordinate to the High Court, if the High Court is satisfied that the case involves a substantial question of law.',
                'procedure' => 'Second appeal on substantial question of law.',
                'court_level' => 'High Court',
                'timeline_requirements' => '90 days from appellate decree.',
                'appeal_provisions' => 'Limited to substantial questions of law.',
            ],
            [
                'section_number' => 115,
                'title' => 'Revision',
                'description' => 'The High Court may call for the record of any case which has been decided by any Court subordinate to such High Court and in which no appeal lies thereto, and if such subordinate Court appears to have exercised a jurisdiction not vested in it by law, or to have failed to exercise a jurisdiction so vested, or to have acted in the exercise of its jurisdiction illegally or with material irregularity.',
                'procedure' => 'High Court revision of subordinate court decisions.',
                'court_level' => 'High Court',
                'timeline_requirements' => '90 days from order.',
            ],
            [
                'section_number' => 144,
                'title' => 'Application for restitution',
                'description' => 'Where and in so far as a decree or an order is varied or reversed, the Court of first instance shall, on the application of any party entitled to any benefit by way of restitution, cause such restitution to be made as will, so far as may be, place the parties in the position which they would have occupied but for such decree or order or such part thereof as has been varied or reversed.',
                'procedure' => 'Application for restitution when decree varied or reversed.',
                'court_level' => 'Civil Court',
                'timeline_requirements' => 'Within 3 years from date of reversal.',
                'appeal_provisions' => 'Restoration of original position.',
            ],
            [
                'section_number' => 151,
                'title' => 'Saving of inherent powers of Court',
                'description' => 'Nothing in this Code shall be deemed to limit or otherwise affect the inherent power of the Court to make such orders as may be necessary for the ends of justice or to prevent abuse of the process of the Court.',
                'procedure' => 'Court has inherent powers for ends of justice.',
                'court_level' => 'Civil Court',
                'timeline_requirements' => 'At any stage.',
            ],
        ];

        foreach ($cpcSections as $section) {
            CpcSection::updateOrCreate(
                ['section_number' => $section['section_number']],
                $section
            );
        }
    }
}
