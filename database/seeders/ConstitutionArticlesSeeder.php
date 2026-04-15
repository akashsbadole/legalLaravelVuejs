<?php

namespace Database\Seeders;

use App\Models\ConstitutionArticle;
use Illuminate\Database\Seeder;

class ConstitutionArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $constitutionArticles = [
            [
                'article_number' => 12,
                'part' => 'III',
                'title' => 'Definition of State',
                'content' => 'In this Part, unless the context otherwise requires, "the State" includes the Government and Parliament of India and the Government and the Legislature of each of the States and all local or other authorities within the territory of India or under the control of the Government of India.',
                'category' => 'Fundamental Rights',
                'explanation' => 'Defines what constitutes "the State" for purposes of Part III (Fundamental Rights).',
            ],
            [
                'article_number' => 14,
                'part' => 'III',
                'title' => 'Equality before law',
                'content' => 'The State shall not deny to any person equality before the law or the equal protection of the laws within the territory of India.',
                'category' => 'Fundamental Rights',
                'explanation' => 'Guarantees equality before law and equal protection of laws to all persons.',
            ],
            [
                'article_number' => 15,
                'part' => 'III',
                'title' => 'Prohibition of discrimination on grounds of religion, race, caste, sex or place of birth',
                'content' => 'The State shall not discriminate against any citizen on grounds only of religion, race, caste, sex, place of birth or any of them.',
                'category' => 'Fundamental Rights',
                'explanation' => 'Prohibits discrimination by State on prohibited grounds.',
            ],
            [
                'article_number' => 16,
                'part' => 'III',
                'title' => 'Equality of opportunity in matters of public employment',
                'content' => 'There shall be equality of opportunity for all citizens in matters relating to employment or appointment to any office under the State.',
                'category' => 'Fundamental Rights',
                'explanation' => 'Ensures equal opportunity in public employment.',
            ],
            [
                'article_number' => 19,
                'part' => 'III',
                'title' => 'Protection of certain rights regarding freedom of speech, etc.',
                'content' => 'All citizens shall have the right to freedom of speech and expression; to assemble peaceably and without arms; to form associations or unions; to move freely throughout the territory of India; to reside and settle in any part of the territory of India; and to practise any profession, or to carry on any occupation, trade or business.',
                'category' => 'Fundamental Rights',
                'explanation' => 'Guarantees six fundamental freedoms to citizens.',
            ],
            [
                'article_number' => 21,
                'part' => 'III',
                'title' => 'Protection of life and personal liberty',
                'content' => 'No person shall be deprived of his life or personal liberty except according to procedure established by law.',
                'category' => 'Fundamental Rights',
                'explanation' => 'Right to life and personal liberty, subject to due process.',
            ],
            [
                'article_number' => '21A',
                'part' => 'III',
                'title' => 'Right to education',
                'content' => 'The State shall provide free and compulsory education to all children of the age of six to fourteen years in such manner as the State may, by law, determine.',
                'category' => 'Fundamental Rights',
                'explanation' => 'Makes education a fundamental right for children aged 6-14.',
            ],
            [
                'article_number' => 32,
                'part' => 'III',
                'title' => 'Remedies for enforcement of rights conferred by this Part',
                'content' => 'The right to move the Supreme Court by appropriate proceedings for the enforcement of the rights conferred by this Part is guaranteed.',
                'category' => 'Fundamental Rights',
                'explanation' => 'Constitutional remedy through Supreme Court for violation of fundamental rights.',
            ],
            [
                'article_number' => '39A',
                'part' => 'IV',
                'title' => 'Equal justice and free legal aid',
                'content' => 'The State shall secure that the operation of the legal system promotes justice, on a basis of equal opportunity, and shall, in particular, provide free legal aid, by suitable legislation or schemes or in any other way, to ensure that opportunities for securing justice are not denied to any citizen by reason of economic or other disabilities.',
                'category' => 'Directive Principles',
                'explanation' => 'Directive for equal justice and free legal aid.',
            ],
            [
                'article_number' => 44,
                'part' => 'IV',
                'title' => 'Uniform civil code for the citizens',
                'content' => 'The State shall endeavour to secure for the citizens a uniform civil code throughout the territory of India.',
                'category' => 'Directive Principles',
                'explanation' => 'Directive to establish uniform civil code.',
            ],
            [
                'article_number' => '51A',
                'part' => 'IVA',
                'title' => 'Fundamental duties',
                'content' => 'It shall be the duty of every citizen of India to abide by the Constitution and respect its ideals and institutions, the National Flag and the National Anthem; to cherish and follow the noble ideals which inspired our national struggle for freedom; to uphold and protect the sovereignty, unity and integrity of India; to defend the country and render national service when called upon to do so; to promote harmony and the spirit of common brotherhood amongst all the people of India transcending religious, linguistic and regional or sectional diversities; to renounce practices derogatory to the dignity of women; to value and preserve the rich heritage of our composite culture; to protect and improve the natural environment including forests, lakes, rivers and wild life, and to have compassion for living creatures; to develop the scientific temper, humanism and the spirit of inquiry and reform; to strive towards excellence in all spheres of individual and collective activity so that the nation constantly rises to higher levels of endeavour and achievement.',
                'category' => 'Fundamental Duties',
                'explanation' => 'Eleven fundamental duties of every Indian citizen.',
            ],
            [
                'article_number' => 72,
                'part' => 'V',
                'title' => 'Power of President to grant pardons, etc., and to suspend, remit or commute sentences in certain cases',
                'content' => 'The President shall have the power to grant pardons, reprieves, respites or remissions of punishment or to suspend, remit or commute the sentence of any person convicted of any offence.',
                'category' => 'Union Executive',
                'explanation' => 'Presidential power to pardon, reprieve, etc. in certain cases.',
            ],
            [
                'article_number' => 124,
                'part' => 'V',
                'title' => 'Establishment and constitution of Supreme Court',
                'content' => 'There shall be a Supreme Court of India consisting of a Chief Justice of India and, until Parliament by law prescribes a larger number, of not more than seven other Judges.',
                'category' => 'Union Judiciary',
                'explanation' => 'Establishes the Supreme Court of India.',
            ],
            [
                'article_number' => 141,
                'part' => 'VI',
                'title' => 'Law declared by Supreme Court to be binding on all courts',
                'content' => 'The law declared by the Supreme Court shall be binding on all courts within the territory of India.',
                'category' => 'Union Judiciary',
                'explanation' => 'Supreme Court decisions binding on all courts.',
            ],
            [
                'article_number' => 226,
                'part' => 'VI',
                'title' => 'Power of High Courts to issue certain writs',
                'content' => 'Notwithstanding anything in Article 32, every High Court shall have power, throughout the territories in relation to which it exercises jurisdiction, to issue to any person or authority, including in appropriate cases, any Government, within those territories directions, orders or writs, including writs in the nature of habeas corpus, mandamus, prohibition, quo warranto and certiorari, or any of them, for the enforcement of any of the rights conferred by Part III and for any other purpose.',
                'category' => 'Union Judiciary',
                'explanation' => 'Writ jurisdiction of High Courts.',
            ],
            [
                'article_number' => '300A',
                'part' => 'XII',
                'title' => 'Persons not to be deprived of property save by authority of law',
                'content' => 'No person shall be deprived of his property save by authority of law.',
                'category' => 'Finance',
                'explanation' => 'Right to property, subject to authority of law.',
            ],
            [
                'article_number' => 368,
                'part' => 'XX',
                'title' => 'Power of Parliament to amend the Constitution and procedure therefor',
                'content' => 'Notwithstanding anything in this Constitution, Parliament may in exercise of its constituent power amend by way of addition, variation or repeal any provision of this Constitution in accordance with the procedure laid down in this article.',
                'category' => 'Amendment',
                'explanation' => 'Procedure for constitutional amendments.',
            ],
        ];

        foreach ($constitutionArticles as $article) {
            ConstitutionArticle::updateOrCreate(
                ['article_number' => $article['article_number']],
                $article
            );
        }
    }
}
