<?php

namespace Database\Seeders;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\AttributeOption;
use App\Models\AttributeOptionUiBehavior as AttributeOptionUiBehaviorModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class AttributeOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createOrUpdateAttributes(AttributeOptionName::TAXA_CLASSES, [
            __('Aves'),
            __('Mammalia'),
            __('Amphibia'),
            __('Reptilia')
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::TAXA_MORPHS, [
            __('Albino'),
            __('Black'),
            __('Blue'),
            __('Brown'),
            __('Dark'),
            __('Gray'),
            __('Leucistic'),
            __('Light'),
            __('Piebald'),
            __('Red'),
            __('Silver'),
            __('White'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES, [
            __('Injured'),
            __('Entrapment'),
            __('Contamination'),
            __('Orphaned'),
            __('Conflict'),
            __('Grounded'),
            __('Re-nesting'),
            __('Illegal activity'),
            __('Undiagnosed illness'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::HOTLINE_ADMINISTRATIVE_CATEGORIES, [
            __('General information'),
            __('Volunteer opportunities'),
            __('Event opportunities'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::HOTLINE_OTHER_CATEGORIES, [
            __('Other'),
            __('Unknown'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::HOTLINE_STATUSES, [
            __('Open'),
            __('Unresolved'),
            __('Resolved'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::HOTLINE_STATUSES,
            __('Open'),
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::HOTLINE_STATUSES,
            __('Unresolved'),
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_UNRESOLVED,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::HOTLINE_STATUSES,
            __('Resolved'),
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::PATIENT_DISPOSITIONS, [
            __('Pending'),
            __('Released'),
            __('Transferred'),
            __('Dead on arrival'),
            __('Died +24hr'),
            __('Died in 24hr'),
            __('Euthanized +24hr'),
            __('Euthanized in 24hr'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            __('Pending'),
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            __('Released'),
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            __('Transferred'),
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            __('Dead on arrival'),
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_DOA,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            [
                __('Died +24hr'),
                __('Died in 24hr'),
            ],
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_DIED,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            [
                __('Euthanized +24hr'),
                __('Euthanized in 24hr'),
            ],
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_EUTHANIZED,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            [
                __('Dead on arrival'),
                __('Died +24hr'),
                __('Died in 24hr'),
                __('Euthanized +24hr'),
                __('Euthanized in 24hr'),
            ],
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_DEAD,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::PATIENT_RELEASE_TYPES, [
            __('Hard'),
            __('Soft'),
            __('Hack'),
            __('Self'),
            __('Returned to rescuer'),
            __('Reunite with family'),
            __('Fostered by animal'),
            __('Adopted by human'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::PATIENT_TRANSFER_TYPES, [
            __('Released'),
            __('Continued care'),
            __('Education or scientific research (individual)'),
            __('Education or scientific research (institute)'),
            __('Falconry or raptor propagation'),
            __('Other'),
         ]);

        $this->createOrUpdateAttributes(AttributeOptionName::PATIENT_LOCATION_FACILITIES, [
            __('Clinic'),
            __('Off-site'),
            __('Homecare'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_LOCATION_FACILITIES,
            __('Clinic'),
            AttributeOptionUiBehavior::PATIENT_LOCATION_FACILITIES_IS_CLINIC,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_LOCATION_FACILITIES,
            __('Off-site'),
            AttributeOptionUiBehavior::PATIENT_LOCATION_FACILITIES_IS_OFF_SITE,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::PATIENT_LOCATION_FACILITIES,
            __('Homecare'),
            AttributeOptionUiBehavior::PATIENT_LOCATION_FACILITIES_IS_HOMECARE,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_TYPES, [
            __('Field'),
            __('Intake'),
            __('Daily'),
            __('Release'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_TYPES,
            __('Field'),
            AttributeOptionUiBehavior::EXAM_TYPE_IS_FIELD,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_TYPES,
            __('Intake'),
            AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_TYPES,
            __('Daily'),
            AttributeOptionUiBehavior::EXAM_TYPE_IS_DAILY,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_TYPES,
            __('Release'),
            AttributeOptionUiBehavior::EXAM_TYPE_IS_RELEASE,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_TYPES,
            [
                __('Intake'),
                __('Release'),
            ],
            AttributeOptionUiBehavior::EXAM_TYPE_CAN_ONLY_OCCUR_ONCE,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_AVES_AGE_UNITS, [
            __('Egg'),
            __('Hatchling / Chick'),
            __('Nestling'),
            __('Fledgling'),
            __('Juvenile'),
            __('Adult'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_AVES_AGE_UNITS,
            [
                __('Adult'),
                __('Juvenile'),
            ],
            AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_MAMMALIA_AGE_UNITS, [
            __('Neonate'),
            __('Infant'),
            __('Juvenile'),
            __('Sub-adult'),
            __('Adult'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_MAMMALIA_AGE_UNITS,
            [
                __('Adult'),
                __('Sub-adult'),
                __('Juvenile'),
            ],
            AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_AMPHIBIA_AGE_UNITS, [
            __('Egg'),
            __('Hatchling'),
            __('Juvenile'),
            __('Adult'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_AMPHIBIA_AGE_UNITS,
            [
                __('Adult'),
                __('Juvenile'),
            ],
            AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_REPTILIA_AGE_UNITS, [
            __('Egg'),
            __('Hatchling'),
            __('Juvenile'),
            __('Adult'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_REPTILIA_AGE_UNITS,
            [
                __('Adult'),
                __('Juvenile'),
            ],
            AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_CHRONOLOGICAL_AGE_UNITS, [
            __('Days'),
            __('Weeks'),
            __('Months'),
            __('Years'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_CHRONOLOGICAL_AGE_UNITS,
            __('Years'),
            AttributeOptionUiBehavior::EXAM_AGE_IS_MATURE,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_DEHYDRATIONS, [
            __('None'),
            __('Mild'),
            __('Moderate'),
            __('Severe'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_WEIGHT_UNITS, [
            'g' => 'g',
            'kg' => 'kg',
            'oz' => 'oz',
            'lb' => 'lb',
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_WEIGHT_UNITS,
            'g',
            AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_G,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_WEIGHT_UNITS,
            'kg',
            AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_KG,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_WEIGHT_UNITS,
            'oz',
            AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_OZ,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_WEIGHT_UNITS,
            'lb',
            AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_LB,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_TEMPERATURE_UNITS, [
            'C' => 'Celsius',
            'F' => 'Fahrenheit',
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_TEMPERATURE_UNITS,
            'Celsius',
            AttributeOptionUiBehavior::EXAM_WEIGHT_TEMPERATURE_IS_C,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_TEMPERATURE_UNITS,
            'Fahrenheit',
            AttributeOptionUiBehavior::EXAM_WEIGHT_TEMPERATURE_IS_F,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_MUCUS_MEMBRANE_COLORS, [
            __('Pink'),
            __('Pale'),
            __('White'),
            __('Blue'),
            __('Yellow'),
            __('Pigmented'),
            __('Dark red'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_MUCUS_MEMBRANE_TEXTURES, [
            __('Moist'),
            __('Tacky'),
            __('Dry'),
            __('Oily'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_SEXES, [
            __('Unknown'),
            __('Male'),
            __('Female'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_BODY_CONDITIONS, [
            __('Emaciated'),
            __('Thin'),
            __('Reasonable'),
            __('Good'),
            __('Plump'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_ATTITUDES, [
            __('Alert'),
            __('Quiet'),
            __('Depressed'),
            __('Obtunded'),
            __('Stuporous'),
            __('Non-responsive'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::EXAM_BODY_PART_FINDINGS, [
            __('Not examined'),
            __('No significant findings'),
            __('Abnormal'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::EXAM_BODY_PART_FINDINGS,
            __('Abnormal'),
            AttributeOptionUiBehavior::EXAM_BODY_PART_FINDING_IS_ABNORMAL,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::PERSON_ENTITY_TYPES, [
            __('General Public'),
            __('Individual Wildlife Rehabilitator'),
            __('Wildlife Rehabilitation Organization'),
            __('For-profit Organization'),
            __('Non-profit Organization'),
            __('Federal (National) Agency / Biologist'),
            __('State (Regional) Agency / Biologist'),
            __('Local Animal Control Agency'),
            __('Research Lab'),
            __('Law Enforcement Agency'),
            __('Veterinary Facility'),
            __('Other (specify in notes)'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::DONATION_METHODS, [
            __('Cash'),
            __('Check'),
            __('Credit Card'),
            __('In-Kind'),
            __('Bank Transfer'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DONATION_METHODS,
            __('Cash'),
            AttributeOptionUiBehavior::DONATION_METHOD_IS_CASH,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_ASSIGNMENTS, [
            __('Veterinarian'),
            __('Technician'),
            __('Assistant'),
            __('Volunteer'),
            __('Husbandry'),
            __('Move'),
            __('Administrative'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_ASSIGNMENTS,
            __('Veterinarian'),
            AttributeOptionUiBehavior::DAILY_TASK_ASSIGNMENT_IS_VETERINARIAN,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_FREQUENCIES, [
            'sd' => __('Once'),
            'sid' => __('Once daily (sid)'),
            'bid' => __('2 times daily (bid)'),
            'tid' => __('3 times daily (tid)'),
            'qid' => __('4 times daily (qid)'),
            '5xd' => __('5 times daily (5xd)'),
            '6xd' => __('6 times daily (6xd)'),
            'q2d' => __('Every 2 days (q2d)'),
            'q3d' => __('Every 3 days (q3d)'),
            'q4d' => __('Every 4 days (q4d)'),
            'q5d' => __('Every 5 days (q5d)'),
            'q75' => __('Once a week (q7d)'),
            'q14d' => __('Every 2 weeks (q14d)'),
            'q21d' => __('Every 3 weeks (q21d)'),
            'q28d' => __('Every 4 weeks (q28d)'),
            'ac' => __('Before eating (ac)'),
            'pc' => __('After eating (pc)'),
            'om' => __('Every morning (om)'),
            'on' => __('Every evening (on)'),
            'prn' => __('As Needed (prn)'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('Once'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('Every 2 days (q2d)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('Every 3 days (q3d)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_3_DAYS,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('Every 4 days (q4d)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_4_DAYS,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('Every 5 days (q5d)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_5_DAYS,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('Once a week (q7d)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_7_DAYS,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('Every 2 weeks (q14d)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_14_DAYS,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('Every 3 weeks (q21d)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_21_DAYS,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('Every 4 weeks (q28d)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_28_DAYS,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            [
                __('Every 2 days (q2d)'),
                __('Every 3 days (q3d)'),
                __('Every 4 days (q4d)'),
                __('Every 5 days (q5d)'),
                __('Once a week (q7d)'),
                __('Every 2 weeks (q14d)'),
                __('Every 3 weeks (q21d)'),
                __('Every 4 weeks (q28d)'),
            ],
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('2 times daily (bid)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_2_DAILY,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('3 times daily (tid)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_3_DAILY,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('4 times daily (qid)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_4_DAILY,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('5 times daily (5xd)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_5_DAILY,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            __('6 times daily (6xd)'),
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_6_DAILY,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS, [
            __('mg/ml'),
            __('mg/tab'),
            __('mg/cap'),
            __('mg/g'),
            __('mg/L'),
            __('g/L'),
            __('iu/cap'),
            __('iu/ml'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            __('mg/ml'),
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            __('mg/tab'),
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_TAB,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            __('mg/cap'),
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_CAP,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            __('mg/g'),
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_GRAM,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            __('iu/cap'),
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU_PER_CAP,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            __('iu/ml'),
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU_PER_ML,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            [
                __('mg/ml'),
                __('mg/tab'),
                __('mg/cap'),
                __('mg/g'),
                __('mg/L'),
            ],
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            [
                __('iu/cap'),
                __('iu/ml'),
            ],
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_IU,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_DOSAGE_UNITS, [
            __('mg/kg'),
            __('ml/kg'),
            __('IU/kg'),
            __('mg/lb'),
            __('ml/lb'),
            __('IU/lb'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            __('mg/kg'),
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            __('IU/kg'),
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_IU_PER_KG,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            __('mg/lb'),
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_LB,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            __('IU/lb'),
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_IU_PER_LB,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            [
                __('mg/kg'),
                __('ml/kg'),
                __('IU/kg'),
            ],
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_KG,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            [
                __('mg/lb'),
                __('ml/lb'),
                __('IU/lb'),
            ],
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_PER_LB,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_DOSE_UNITS, [
            'milliliters (ml)',
            'cc',
            'drops (gt)',
            'grams (g)',
            'milligrams (mg)',
            'tab',
            'cap',
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            'milliliters (ml)',
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            'cap',
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_CAP,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            'tab',
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_TAB,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            'grams (g)',
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_GRAM,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_ROUTES, [
            __('Oral (po)'),
            __('Subcutaneous (sq)'),
            __('Intramuscular (im)'),
            __('Intravenous (iv)'),
            __('Intraperitoneal (ip)'),
            __('Intraosseous (io)'),
            __('Intracardiac (ic)'),
            __('Intracoelomic (icl)'),
            __('Topical'),
            __('Intranasal'),
            __('Both eyes (ou)'),
            __('Right eye (od)'),
            __('Left eye (os)'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_NUTRITION_ROUTES, [
            __('Leave in enclosure'),
            __('Hand feed'),
            __('Force feed'),
            __('Tube feed'),
            __('Gavage'),

        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES, [
            'min' => __('Minutes'),
            'hrs' => __('Hours'),
            'days' => __('Days'),
            'wks' => __('Weeks'),
            'sid' => __('Once daily (sid)'),
            'bid' => __('2 times daily (bid)'),
            'tid' => __('3 times daily (tid)'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES,
            __('Hours'),
            AttributeOptionUiBehavior::DAILY_TASK_NUTRITION_FREQUENCY_IS_HOURS,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_NUTRITION_ROUTES, [
            __('Leave in enclosure'),
            __('Hand feed'),
            __('Force feed'),
            __('Gavage'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::DAILY_TASK_NUTRITION_INGREDIENT_UNITS, [
            __('Milliliters'),
            __('Grams'),
            __('Kilograms'),
            __('Cups'),
            __('Teaspoons'),
            __('Tablespoons'),
            __('Bowls'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::NECROPSY_CARCASS_CONDITIONS, [
            __('Fresh'),
            __('Fair (decomposed, organs intact)'),
            __('Poor (advanced decomposition)'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::NECROPSY_SAMPLES, [
            __('Liver'),
            __('Spleen'),
            __('Kidney'),
            __('Bursa'),
            __('GI Tract'),
            __('Heart'),
            __('Lung'),
            __('Fat'),
            __('Skin'),
            __('Muscle'),
            __('Bone'),
            __('Brain'),
            __('Other'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::NECROPSY_SAMPLES,
            __('Other'),
            AttributeOptionUiBehavior::NECROPSY_SAMPLES_IS_OTHER,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_AGE_CODES, [
            'U' => __('Unknown'),
            'AHY' => __('After Hatching Year'),
            'HY' => __('Hatching Year'),
            'J' => __('Juvenile'),
            'L' => __('Local'),
            'SY' => __('Second Year'),
            'ASY' => __('After Second Year'),
            'TY' => __('Third Year'),
            'ATY' => __('After Third Year'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_HOW_AGED_CODES, [
            'AM' => __('Auxiliary Marker on bird at capture'),
            'BO' => __('Behavioral observation'),
            'BP' => __('Brood patch'),
            'BU' => __('Bursa of Fabricius'),
            'CA' => __('Calendar'),
            'CC' => __('Combination of characteristics / measurements'),
            'CL' => __('Cloaca'),
            'EG' => __('Egg in oviduct'),
            'EY' => __('Eye color'),
            'FB' => __('Fault bar'),
            'FF' => __('Flight feathers (remiges), condition or color'),
            'IC' => __('Inconclusive, Conflicting'),
            'LP' => __('Molt limit present'),
            'MB' => __('Mouth / bill'),
            'MR' => __('Actively-molting remiges'),
            'NA' => __('Not attempted'),
            'NF' => __('Nestling recently fledged, incapable of powered flight'),
            'NL' => __('No molt limit'),
            'NN' => __('Nestling in nest (altricials), downy young (precocials)'),
            'OT' => __('Other'),
            'PC' => __('Primary covert wear and / or shape'),
            'PL' => __('Body Plumage'),
            'RC' => __('Re-captured bird with USGS band'),
            'SK' => __('Skull'),
            'TL' => __('Tail length'),
            'TS' => __('Tail shape or wear'),
            '0' => __('Cloacal examination (waterfowl only)'),
            '1' => __('Adult plumage'),
            '2' => __('Juvenal plumage'),
            '3' => __('Eye color'),
            'A' => __('Nestling in nest--no flight feathers present'),
            'B' => __('Nestling in nest--flight feathers in pin'),
            'C' => __('Nestling recently fledged, incapable of powered flight'),
            'F' => __('Feather wear in the flight feathers'),
            'J' => __('Retained juvenal plumage (wispy tertials / notched tail etc)'),
            'L' => __('Molt limit present'),
            'M' => __('Multiple ages of remiges (wing feathers)'),
            'N' => __('Molt limit absent'),
            'P' => __('Primary covert shape and / or primary feather shape / wear'),
            'S' => __('Skulling (not including pinhole windows)'),
            'T' => __('Tail shape and tail wear'),
            'W' => __('Pinhole windows (see skulling)'),
            'X' => __('not attempted'),
            'Z' => __('web tag (or other auxiliary marker)'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_SEX_CODES, [
            'U' => __('Unknown'),
            'M' => __('Male'),
            'F' => __('Female'),
            'm' => __('Male, sexed upon recapture'),
            'f' => __('Female, sexed upon recapture'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_HOW_SEXED_CODES, [
            'BO' => __('Behavioral observation'),
            'BP' => __('Brood patch'),
            'CC' => __('Combination of characteristics / measurements'),
            'CL' => __('Cloaca'),
            'DN' => __('DNA/chromosome analysis'),
            'EG' => __('Egg in oviduct'),
            'EY' => __('Eye color'),
            'FS' => __('Feather Shape (Primaries or tail)'),
            'IC' => __('Inconclusive, Conflicting'),
            'LL' => __('Laparotomy / laparoscopy'),
            'MB' => __('Mouth/bill'),
            'NA' => __('Not attempted'),
            'OT' => __('Other'),
            'PL' => __('Body Plumage'),
            'RC' => __('Sexed upon recapture'),
            'TL' => __('Tail length'),
            'WL' => __('Wing length'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_STATUS_CODES, [
            '-' => __('Dead bird (banding-related mortality)'),
            '2' => __('Transported'),
            '3' => __('Normal wild bird'),
            '4' => __('Hand-reared, game-farm or hacked bird'),
            '5' => __('Sick, Exhausted, Injured, Crippled, or Physical Deformity'),
            '6' => __('Obsolete - Experimental bird'),
            '7' => __('Rehabilitated and held'),
            '8' => __('Held for longer than 24 hours for experimental or other purposes'),
            '9' => __('Obsolete - Dog caught bird'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_ADDITIONAL_INFORMATION_STATUS_CODES, [
            '-' => __('Banding or trapping mortality (no band, aux. marker or other info applied)'),
            '00' => __('Federal numbered metal band only'),
            '01' => __('Colored leg band(s): plastic, metal, paint, tape'),
            '02' => __('Neck collar - usually coded'),
            '03' => __('Reward band (Federal or State)'),
            '04' => __('Control band (Reward band studies only)'),
            '06' => __('Misc. metal band (State, Provincial etc) with address or telephone number, plus Federal band'),
            '07' => __('Double-banded (Two Federal bands placed on a bird at the same time)'),
            '08' => __('Temporary markers: Paint or dye; other temporary markers on feathers (imping, tape on tail)'),
            '09' => __('All flight feathers on one or both wings clipped or pulled upon release'),
            '10' => __('All flight feathers on one or both wings clipped or pulled plus auxiliary marker(s)'),
            '11' => __('Sexed by laparotomy or laparoscopy'),
            '12' => __('Sexed by laparotomy or laparoscopy, plus auxiliary marker(s)'),
            '14' => __('Mouth swab'),
            '15' => __('Mouth swab, plus one or more auxiliary markers used'),
            '16' => __('Tracheal swab'),
            '17' => __('Tracheal swab, plus one or more auxiliary markers used'),
            '18' => __('Blood sample taken'),
            '19' => __('Blood sample taken, plus auxiliary marker(s)'),
            '20' => __('Fostered or cross-fostered into wild nests'),
            '21' => __('Fostered or cross-fostered into wild nests, plus auxiliary marker(s)'),
            '25' => __('Two or more types of auxiliary markers'),
            '29' => __('Miscellaneous band, Federal band, plus auxiliary marker(s)'),
            '30' => __('Double-banded with Federal bands, plus auxiliary marker(s)'),
            '33' => __('Taken from an artificial nest structure (eg, nest boxes, platforms, etc)'),
            '34' => __('Taken from an artificial nest structure, plus auxiliary marker(s)'),
            '39' => __('Wing, patagial, head, back, and / or nape tag(s)'),
            '40' => __('Oiled'),
            '41' => __('Oiled, plus one or more auxiliary markers used'),
            '51' => __('Nasal saddle and nasal discs or other bill marker'),
            '59' => __('Web tagged, usually coded'),
            '69' => __('Flag, streamer, or tab on leg'),
            '70' => __('Captured by spotlighting'),
            '71' => __('Captured by spotlighting, plus auxiliary marker(s)'),
            '75' => __('PIT tag'),
            '80' => __('Satellite/Cell/GPS transmitter'),
            '81' => __('Radio transmitter'),
            '85' => __('Miscellaneous (combination or situation not covered by other ai codes)'),
            '87' => __('Captured with drugs or tranquilizers'),
            '88' => __('Captured with drugs or tranquilizers, plus auxiliary marker(s)'),
            '89' => __('Transmitter. (Obsolete, see 80, 81)'),
            '90' => __('Data logger (including geolocators)'),
            '99' => __('(none)'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_DISPOSITION_CODES, [
            '1' => __('New Banding'),
            '4' => __('Destroyed'),
            '5' => __('Replacememt'),
            '6' => __('Added Band'),
            '8' => __('Band Lost'),
            '9' => __('Record Lost'),
            'D' => __('Double Band First'),
            'S' => __('Double Band Second'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_RECAPTURE_DISPOSITION_CODES, [
            'F' => __('Foreign recapture'),
            'R' => __('Recapture'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_PRESENT_CONDITION_CODES, [
            '00' => __('Unknown - Unknown condition of band'),
            '01' => __('Unknown - Band left on bird'),
            '02' => __('Unknown - Band removed from bird'),
            '03' => __('Dead - Unknown condition of band'),
            '04' => __('Dead - Band left on bird'),
            '05' => __('Dead - Band removed from bird'),
            '06' => __('Alive - Released, Unknown condition of band'),
            '07' => __('Alive - Released, Band left on bird'),
            '08' => __('Alive - Released, Band removed from bird'),
            '09' => __('Alive - In Captivity, Unknown condition of band'),
            '10' => __('Alive - In Captivity, Band left on bird'),
            '11' => __('Alive - In Captivity, Band removed from bird'),
            '12' => __('Alive - Unknown, Unknown condition of band'),
            '13' => __('Alive - Unknown, Band left on bird'),
            '14' => __('Alive - Unknown, Band removed from bird'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_HOW_PRESENT_CONDITION_CODES, [
            '00' => __('Found dead'),
            '01' => __('Shot'),
            '02' => __('Caught or found dead due to starvation'),
            '03' => __('Caught due to injury'),
            '04' => __('Caught by or due to traps or snares other than devices used to catch birds for banding. (Muskrat traps, pole traps, etc.)'),
            '06' => __('Caught by or due to rodent'),
            '07' => __('Caught by or due to miscellaneous birds'),
            '08' => __('Caught by or due to shrike'),
            '09' => __('Caught by or due to hawks, owls, or other raptors. Also includes bands found in raptor pellets'),
            '10' => __('Banding Mortality birds accidentally killed during banding operations. Includes birds killed in, by or due to traps, holding devices or handling. Does not include birds killed by weather or predators'),
            '11' => __('Caught by or due to dog'),
            '12' => __('Caught by or due to cat'),
            '13' => __('Caught due to striking stationary object other than wires or towers'),
            '14' => __('Caught due to striking or being struck by motor vehicle'),
            '15' => __('Caught or found dead due to weather conditions'),
            '16' => __('Collected as Scientific Specimen or captured for a Scientific Study'),
            '17' => __('Drowned'),
            '18' => __('Caught or found dead due to disease botulism (part of confirmed die-off)'),
            '19' => __('Caught by or due to reptile'),
            '20' => __('Caught due to disease'),
            '21' => __('Bird caught or found dead in building or enclosure'),
            '23' => __('Caught or found dead due to oil or tar'),
            '24' => __('Caught or killed due to fall from nest'),
            '25' => __('Caught or killed due to Poisoning Does not include lead poisoning, avicides or pesticides. See codes 40, 44, 55'),
            '26' => __('Caught by or due to entanglement in fishing gear (line, hooks, nets, etc.)'),
            '27' => __('Caught by or found dead due to striking or being struck by moving train'),
            '28' => __('Caught by hand'),
            '29' => __('Sight record. Identified by color band, marked plumage or marker other than standard, numbered metal band'),
            '30' => __('Died in nest'),
            '31' => __('Caught by or due to miscellaneous animal. See also codes 06, 11, 12, 49, 51'),
            '32' => __('Caught due to parasite infestation'),
            '33' => __('Caught or observed at or in nest. See also codes 30, 49'),
            '34' => __('Caught or found dead due to fish (includes bands reported found inside fish)'),
            '36' => __('Caught due to exhaustion'),
            '39' => __('Caught or found dead due to striking or being struck by moving aircraft'),
            '40' => __('Caught or found dead due to Lead poisoning'),
            '42' => __('Caught due to striking or being struck by moving farm machinery'),
            '43' => __('Caught or found dead due to disease trichomoniasis'),
            '44' => __('Caught or found dead due to control operations (roost bombing, gassing, avicides, wetting agents, etc.) See also code 55'),
            '45' => __('Found dead or injured on highway. No information as to whether hit by motor vehicle or not. See also code 14'),
            '46' => __('Caught due to joined flock of domestic or captive birds or fowl'),
            '49' => __('Caught at, on or in nest by predator. See also codes 24, 30, 33'),
            '50' => __('Found dead, band with skeleton or bone only'),
            '51' => __('Banding mortality. Bird killed by predators, weather, etc. while in trapping or holding devices. See also code 10'),
            '52' => __('Sight record. Band read by telescope or other means while bird was free. See also code 29'),
            '53' => __('Captured for Scientific Purposes (not collected). Bird captured, Status changed(dyed, neck-banded, bled, etc.) for scientific purposes bird released. See also code 16'),
            '54' => __('Caught due to striking radio, TV, high tension, etc. wires or towers, or ceilometers. See also code 37'),
            '55' => __('Caught due to pesticides. Birds reported killed or captured as a result of spray programs. Does not include avicides. See also code 44'),
            '56' => __('Obtained - Letter simply states in effect "I obtained this bird". No further information available. See also code 98'),
            '57' => __('Caught due to entanglement in anything other than fishing gear, e.g., in wire, string, vines, fence, shrubs, etc. See also code 26'),
            '58' => __('Bird located by electronic sensors (Note: location reported is for receiver, and not necessarily the bird)'),
            '61' => __('Found dead or caught due to disease'),
            '62' => __('Found dead or caught due to poisoning'),
            '63' => __('Struck wind turbine'),
            '64' => __('Killed or caught by a predator other than a cat'),
            '66' => __('Previously banded bird trapped and released during banding operations'),
            '70' => __('Purchased, e-Bay, traded, etc'),
            '89' => __('Previously banded bird trapped and released during banding operations in different 10-minute block than where originally banded. See also code 99'),
            '91' => __('Illegally taken. Reported by conservation agency employees or other law enforcement officials as illegally taken'),
            '97' => __('Miscellaneous. Method of recovery not covered by other codes'),
            '98' => __('Band or band number only obtained. No further information available. See also code 56'),
            '99' => __('Previously banded bird trapped and released during banding operations in same 10-minute block where originally banded. See also code 89'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_BAND_SIZES, [
            'X' => '1.27-1.52 mm',
            '0A' => '1.98 mm',
            '0' => '2.11 mm',
            '1C' => '2.31 mm',
            '1' => '2.39 mm',
            '1B' => '2.77 mm',
            '1P' => '2.84 mm',
            '1A' => '3.18 mm',
            '1D' => '3.50 mm',
            '2' => '3.96 mm',
            '2A' => '4.2 mm',
            '3' => '4.78 mm',
            '3B' => '5.16 mm',
            '3A' => '5.56 mm',
            '4' => '6.35 mm',
            '4A' => '7.14 mm',
            '4V' => '',
            '5' => '7.95 mm',
            '5A' => '8.74 mm',
            '5R' => '',
            '6' => '9.53 mm',
            '6M' => '',
            '7A' => '11.13 mm',
            '7' => '12.70 mm',
            '7V' => '',
            '7B' => '13.49 mm',
            '7D' => '15.0 mm',
            '8V' => '',
            '8' => '17.48 mm',
            '8A' => '20.65 mm',
            '9' => '22.23 mm',
            '9A' => '25.39 mm',
            '9C' => '28.58 mm',
            '9V' => '',
            'EX' => '',
            'UK' => '',
            'N' => '',
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_SAMPLES_COLLECTED, [
            __('Feathers'),
            __('Blood'),
            __('Parasites'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_AUXILLARY_MARKER_TYPE_CODES, [
            '00' => __('Federal Metal Band'),
            '00B' => __('Federal Metal Band (Anodized)'),
            '00C' => __('Federal Metal Band (Painted)'),
            '00D' => __('Tape over Federal Band'),
            '01' => __('Color Leg Band'),
            '01A' => __('Plastic Color Leg Band'),
            '01B' => __('Anodized Color Leg Band'),
            '01C' => __('Painted alum. Color Leg Band'),
            '01D' => __('Tape over alum. Color Leg Band'),
            '02' => __('Neck Collar'),
            '03' => __('Reward Band'),
            '06' => __('Misc. metal band (Foreign, State, Prov. etc.) with inscription'),
            '08A' => __('Breast Dye / Paint'),
            '08B' => __('Tail Dye / Paint'),
            '08C' => __('Wing Dye / Paint'),
            '08D' => __('Head/Neck Dye / Paint'),
            '08E' => __('Multiple areas Dye / Paint'),
            '08F' => __('Tail streamer Temporary Marker'),
            '08G' => __('Imped feather Temporary Marker'),
            '08H' => __('Glued Temporary Marker'),
            '08I' => __('Misc. Temporary Marker'),
            '39' => __('Wing / Head / Back Tag (obsolete)'),
            '39A' => __('Wing Tag'),
            '39B' => __('Back Tag'),
            '39C' => __('Head Tag'),
            '51A' => __('Nasal Saddle'),
            '51B' => __('Disk Nasal Marker'),
            '59' => __('Web / Toe Tag'),
            '69' => __('Leg Flag'),
            '75A' => __('PIT tag (tail mount)'),
            '75B' => __('PIT tag (back pack)'),
            '75C' => __('PIT tag (leg attachment)'),
            '75D' => __('PIT tag (surgical implant)'),
            '75E' => __('PIT tag (subcutaneous)'),
            '75F' => __('PIT tag (neck attachment)'),
            '75G' => __('PIT tag (other)'),
            '75H' => __('PIT tag (leg-loop harness)'),
            '75J' => __('PIT tag (glue to skin or feathers)'),
            '75K' => __('PIT tag (prong and suture)'),
            '80A' => __('Satellite / Cell / GPS Transmitter (tail mount)'),
            '80B' => __('Satellite / Cell / GPS Transmitter (back pack)'),
            '80C' => __('Satellite / Cell / GPS Transmitter (leg attachment)'),
            '80D' => __('Satellite / Cell / GPS Transmitter (surgical implant)'),
            '80E' => __('Satellite / Cell / GPS Transmitter (subcutaneous)'),
            '80F' => __('Satellite / Cell / GPS Transmitter (neck attachment)'),
            '80G' => __('Satellite / Cell / GPS Transmitter (other)'),
            '80H' => __('Satellite / Cell / GPS Transmitter (leg-loop harness)'),
            '80J' => __('Satellite / Cell / GPS Transmitter (glue to skin or feathers)'),
            '80K' => __('Satellite / Cell / GPS Transmitter (prong and suture)'),
            '81A' => __('Radio Transmitter (tail mount)'),
            '81B' => __('Radio Transmitter (back pack)'),
            '81C' => __('Radio Transmitter (leg attachment)'),
            '81D' => __('Radio Transmitter (surgical implant)'),
            '81E' => __('Radio Transmitter (subcutaneous)'),
            '81F' => __('Radio Transmitter (neck attachment)'),
            '81G' => __('Radio Transmitter (other)'),
            '81H' => __('Radio Transmitter (leg-loop harness)'),
            '81J' => __('Radio Transmitter (glue to skin or feathers)'),
            '81K' => __('Radio Transmitter (prong and suture)'),
            '85' => __('Misc'),
            '89' => __('Transmitter (obsolete)'),
            '90A' => __('Data Logger / Geolocator (tail mount)'),
            '90B' => __('Data Logger / Geolocator (back pack)'),
            '90C' => __('Data Logger / Geolocator (leg attachment)'),
            '90D' => __('Data Logger / Geolocator (surgical implant)'),
            '90E' => __('Data Logger / Geolocator (subcutaneous)'),
            '90F' => __('Data Logger / Geolocator (neck attachment)'),
            '90G' => __('Data Logger / Geolocator (other)'),
            '90H' => __('Data Logger / Geolocator (leg-loop harness)'),
            '90J' => __('Data Logger / Geolocator (glue to skin or feathers)'),
            '90K' => __('Data Logger / Geolocator (prong and suture)'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_AUXILLARY_COLOR_CODES, [
            '1' => __('Red'),
            '2' => __('Yellow'),
            '3' => __('Blue'),
            '4' => __('Green'),
            '5' => __('White'),
            '6' => __('Black'),
            '7' => __('Orange'),
            '8' => __('Gray'),
            '9' => __('Purple'),
            '10' => __('Lavender'),
            '11' => __('Gold'),
            '12' => __('Brown'),
            '13' => __('Silver'),
            '14' => __('Pink'),
            '15' => __('Magenta'),
            '16' => __('Mauve'),
            '17' => __('Miscellaneous'),
            '18' => __('Light Blue'),
            '19' => __('Dark Blue'),
            '20' => __('Light Green'),
            '21' => __('Dark Green'),
            '22' => __('Light Pink'),
            '23' => __('Hot Pink'),
            '24' => __('Maroon'),
            '25' => __('Aqua'),
            '26' => __('Azure'),
            '27' => __('Burgundy'),
            '28' => __('Chartreuse'),
            '29' => __('Flesh'),
            '30' => __('Lime'),
            '31' => __('Ochre'),
            '32' => __('Olive'),
            '33' => __('Tan'),
            '34' => __('Turquoise'),
            '99' => __('Multi-color'),
            '100' => __('Red / Cream'),
            '102' => __('Red / Yellow'),
            '103' => __('Red / Blue'),
            '104' => __('Red / Green'),
            '105' => __('Red / White'),
            '106' => __('Red / Black'),
            '107' => __('Red / Orange'),
            '116' => __('Red / Mauve'),
            '118' => __('Red / Light Blue'),
            '119' => __('Red / Dark Blue'),
            '120' => __('Red / Light Green'),
            '121' => __('Red / Dark Green'),
            '122' => __('Red / Light Pink'),
            '134' => __('Red / Turquoise'),
            '201' => __('Yellow / Red'),
            '203' => __('Yellow / Blue'),
            '204' => __('Yellow / Green'),
            '205' => __('Yellow / White'),
            '206' => __('Yellow / Black'),
            '207' => __('Yellow / Orange'),
            '216' => __('Yellow / Mauve'),
            '218' => __('Yellow / Light Blue'),
            '219' => __('Yellow / Dark Blue'),
            '220' => __('Yellow / Light Green'),
            '221' => __('Yellow / Dark Green'),
            '222' => __('Yellow / Light Pink'),
            '223' => __('Yellow / Hot Pink'),
            '301' => __('Blue / Red'),
            '302' => __('Blue / Yellow'),
            '304' => __('Blue / Green'),
            '305' => __('Blue / White'),
            '306' => __('Blue / Black'),
            '307' => __('Blue / Orange'),
            '313' => __('Blue / Silver'),
            '401' => __('Green / Red'),
            '402' => __('Green / Yellow'),
            '403' => __('Green / Blue'),
            '405' => __('Green / White'),
            '406' => __('Green / Black'),
            '407' => __('Green / Orange'),
            '409' => __('Green / Purple'),
            '413' => __('Green / Silver'),
            '416' => __('Green / Mauve'),
            '501' => __('White / Red'),
            '503' => __('White / Blue'),
            '504' => __('White / Green'),
            '506' => __('White / Black'),
            '507' => __('White / Orange'),
            '509' => __('White / Purple'),
            '516' => __('White / Mauve'),
            '518' => __('White / Light Blue'),
            '519' => __('White / Dark Blue'),
            '520' => __('White / Light Green'),
            '521' => __('White / Dark Green'),
            '523' => __('White / Hot Pink'),
            '601' => __('Black / Red'),
            '602' => __('Black / Yellow'),
            '603' => __('Black / Blue'),
            '604' => __('Black / Green'),
            '605' => __('Black / White'),
            '607' => __('Black / Orange'),
            '613' => __('Black / Silver'),
            '618' => __('Black / Light Blue'),
            '621' => __('Black / Dark Green'),
            '701' => __('Orange / Red'),
            '702' => __('Orange / Yellow'),
            '703' => __('Orange / Blue'),
            '704' => __('Orange / Green'),
            '705' => __('Orange / White'),
            '706' => __('Orange / Black'),
            '712' => __('Orange / Brown'),
            '713' => __('Orange / Silver'),
            '716' => __('Orange / Mauve'),
            '719' => __('Orange / Dark Blue'),
            '721' => __('Orange / Dark Green'),
            '723' => __('Orange / Hot Pink'),
            '1307' => __('Silver / Orange'),
            '1601' => __('Mauve / Red'),
            '1602' => __('Mauve / Yellow'),
            '1603' => __('Mauve / Blue'),
            '1604' => __('Mauve / Green'),
            '1605' => __('Mauve / White'),
            '1606' => __('Mauve / Black'),
            '1607' => __('Mauve / Orange'),
            '1621' => __('Mauve / Dark Green'),
            '1623' => __('Mauve / Hot Pink'),
            '1807' => __('Light Blue / Orange'),
            '1816' => __('Light Blue / Mauve'),
            '1819' => __('Light Blue / Dark Blue'),
            '1821' => __('Light Blue / Dark Green'),
            '1822' => __('Light Blue / Light Pink'),
            '1823' => __('Light Blue / Hot Pink'),
            '1901' => __('Dark Blue / Red'),
            '1906' => __('Dark Blue / Black'),
            '1916' => __('Dark Blue / Mauve'),
            '1921' => __('Dark Blue / Dark Green'),
            '1922' => __('Dark Blue / Light Pink'),
            '1923' => __('Dark Blue / Hot Pink'),
            '2006' => __('Light Green / Black'),
            '2007' => __('Light Green / Orange'),
            '2012' => __('Light Green / Brown'),
            '2016' => __('Light Green / Mauve'),
            '2018' => __('Light Green / Light Blue'),
            '2019' => __('Light Green / Dark Blue'),
            '2021' => __('Light Green / Dark Green'),
            '2022' => __('Light Green / Light Pink'),
            '2023' => __('Light Green / Hot Pink'),
            '2206' => __('Light Pink / Black'),
            '2216' => __('Light Pink / Mauve'),
            '2221' => __('Light Pink / Dark Green'),
            '2306' => __('Hot Pink / Black'),
            '2319' => __('Hot Pink / Dark Blue'),
            '2321' => __('Hot Pink / Dark Green'),
            '2322' => __('Hot Pink / Light Pink'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_AUXILLARY_CODE_COLOR, [
            __('Red'),
            __('Yellow'),
            __('Blue'),
            __('White'),
            __('Black'),
            __('Silver'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_AUXILLARY_SIDE_OF_BIRD, [
            __('Left'),
            __('Right'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::BANDING_PLACEMENT_ON_LEG, [
            __('Below the joint'),
            __('Above the joint'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_CONDITIONS, [
            __('Alive'),
            __('Dead'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::OILED_PROCESSING_CONDITIONS,
            __('Alive'),
            AttributeOptionUiBehavior::OILED_PROCESSING_CONDITION_IS_ALIVE,
        );
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::OILED_PROCESSING_CONDITIONS,
            __('Dead'),
            AttributeOptionUiBehavior::OILED_PROCESSING_CONDITION_IS_DEAD,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_STATUSES, [
            '0' => __('(0) No signs of oil detected'),
            '1' => __('(1) Yes - oil visually detected'),
            '2' => __('(2) Yes - smell oil'),
            '3' => __('(3) Yes - skin burned'),
            '4' => __('(4) Unknown - but skin wet / not waterproof'),
            '5' => __('(5) Unknown - but plumage misaligned / parted / sticky'),
            '99' => __('(99) Not evaluated or applicable'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_OILING_PERCENTAGES, [
            '1' => __('(1) <2% of body'),
            '2' => __('(2) 2-25% of body'),
            '3' => __('(3) 26-50% of body'),
            '4' => __('(4) 51-75% of body'),
            '5' => __('(5) 76-100% of body'),
            '6' => __('(6) Oil detected but extent indeterminable due to state of carcass'),
            '7' => __('(7) No oil detected but this may be due to state of carcass (i.e., partial)'),
            '99' => __('(99) Not evaluated or applicable'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_OILING_DEPTHS, [
            '1' => __('(1) Surface (penetrated <1/4 feather / pelage)'),
            '2' => __('(2) Moderate (penetrated <1/2 feather / pelage)'),
            '3' => __('(3) Deep (penetrated to skin)'),
            '99' => __('(99) Not evaluated or applicable'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_OILING_LOCATIONS, [
            '1' => __('(1) Bill / mouth area only'),
            '2' => __('(2) Body (1 spot)'),
            '3' => __('(3) Spotty (spots in multiple areas)'),
            '4' => __('(4) Waterline'),
            '5' => __('(5) Entire body'),
            '99' => __('(99) Not evaluated or applicable'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_EVIDENCES, [
            __('Photo'),
            __('Feathers'),
            __('Pelage'),
            __('Swab'),
            __('Other Tissue'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_CARCASS_CONDITIONS, [
            '1' => __('(1) Fresh / whole / no scavenging'),
            '2' => __('(2) Fresh / whole / scavenged'),
            '3' => __('(3) Decomposing / whole'),
            '4' => __('(4) Fresh / body parts only'),
            '5' => __('(5) Decomposing / body parts only'),
            '6' => __('(6) Dessicated / mummified'),
            '99' => __('(99) Not evaluated'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_EXTENT_OF_SCAVENGINGS, [
            '0' => __('(0) None detected'),
            '1' => __('(1) Light'),
            '2' => __('(2) Moderate'),
            '3' => __('(3) Heavy'),
            '99' => __('(99) Not evaluated'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_OIL_CONDITIONS, [
            __('Fresh'),
            __('Mousse / Frothy'),
            __('Slightly Weathered'),
            __('Highly Weathered'),
            __('Other - Specify'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_PROCESSING_OIL_COLORS, [
            __('Black'),
            __('Brown'),
            __('Yellow'),
            __('Clear'),
            __('Other - Specify'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_WASH_PRE_TREATMENTS, [
            __('None'),
            __('Methyl soyate'),
            __('Methyl oleate'),
            __('Vegetable oil'),
            __('Soap pressure spray'),
            __('Olive oil'),
            __('Canola oil'),
            __('Baking soda paste'),
            __('Other - Specify'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::OILED_WASH_PRE_TREATMENTS,
            __('None'),
            AttributeOptionUiBehavior::OILED_WASH_PRE_TREATMENT_IS_NONE,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_WASH_TYPES, [
            __('Initial wash'),
            __('Re-wash'),
            __('Quick wash'),
            __('Hock / Foot wash'),
        ]);
        $this->createOrUpdateAttributeOptionUiBehaviors(
            AttributeOptionName::OILED_WASH_TYPES,
            __('Initial wash'),
            AttributeOptionUiBehavior::OILED_WASH_TYPE_IS_INITIAL,
        );

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_WASH_DETERGENTS, [
            __('Dawn 2%'),
            __('Dawn higher concentration'),
            __('Other - Specify'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_WASH_DRYING_METHODS, [
            __('Pet dryer'),
            __('Heat lamp'),
            __('Other - Specify'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_CONDITIONING_BUOYANCIES, [
            __('Normal'),
            __('Reduced'),
            __('Sinking'),
            __('Asymmetric'),
            __('Not applicable'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_CONDITIONING_HAULED_OUTS, [
            __('Continuously'),
            __('Frequently'),
            __('Occasionally'),
            __('Rarely or never'),
            __('Not observed'),
            __('Not applicable'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_CONDITIONING_PREENINGS, [
            __('Normal'),
            __('Excessive'),
            __('Reduced'),
            __('None observed'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_CONDITIONING_AREAS_WET_TO_SKIN, [
            __('Head / Neck'),
            __('Left Axillary'),
            __('Righ Axillary'),
            __('Vent'),
            __('Back'),
            __('Chest'),
            __('Keel'),
            __('Left Inguinal'),
            __('Right Inguinal'),
            __('Other - Specify'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::OILED_CONDITIONING_UNKNOWN_BOOL, [
            __('Unknown'),
            __('Yes'),
            __('No')
        ]);




        $this->createOrUpdateAttributes(AttributeOptionName::LAB_BOOLEAN, [
            __('Negative'),
            __('Positive'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::LAB_PLASMA_COLORS, [
            __('Clear'),
            __('Lypemic'),
            __('Hemolyzed'),
            __('Icteric'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::LAB_RESULT_QUANTITY_UNITS, [
            __('Total'),
            __('Estimate'),
            __('Percent'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::LAB_CHEMISTRY_UNITS, [
            'mEq/L',
            'mg/dL',
            'MMOL/L',
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::LAB_URINE_COLLECTION_METHODS, [
            __('Free catch'),
            __('Midstream'),
            __('Manual compression'),
            __('Cystocentesis'),
            __('Catheter'),
            __('Table'),
            __('Floor'),
            __('Cage'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::LAB_URINE_TURBIDITIES, [
            __('Clear'),
            __('Cloudy'),
            __('Flocculent'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::LAB_URINE_ODORS, [
            __('Charac'),
            __('Ammonia'),
            __('Sweet'),
            __('Putrid'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::LAB_TOXINS, [
            __('Lead'),
            __('Mercury'),
            __('Arsenic'),
            __('Cadmium'),
            __('Chromium'),
        ]);

        $this->createOrUpdateAttributes(AttributeOptionName::LAB_TOXIN_LEVEL_UNITS, [
            'ppm',
            'ug/dl',
            'mEq/L',
            'mg/dL',
            'MMOL/L',
        ]);
    }

    private function createOrUpdateAttributes(AttributeOptionName $attributeName, array $values): void
    {
        $isAssoc = Arr::isAssoc($values);

        $index = 0;

        foreach ($values as $key => $value) {
            $attributeOption = AttributeOption::firstOrCreate([
                'name' => $attributeName,
                'value' => $value,
                'code' => $isAssoc ? $key : null
            ], [
                'value_lowercase' => strtolower($value),
                'sort_order' => $index + 1,
            ]);

            if (empty($value)) {
                $attributeOption->delete();
            }

            $index++;
        }
    }

    private function createOrUpdateAttributeOptionUiBehaviors(
        AttributeOptionName $attributeName,
        string|array $value,
        AttributeOptionUiBehavior $attributeOptionUiBehavior
    ): void {
        foreach (Arr::wrap($value) as $value) {
            try {
                $attributeOption = AttributeOption::where('name', '=', $attributeName->value)
                    ->where('value', '=', $value)
                    ->firstOrFail();
            } catch (\Exception $e) {
                dd($attributeName->value, $value);
            }

            AttributeOptionUiBehaviorModel::firstOrCreate([
                'attribute_option_id' => $attributeOption->id,
                'behavior' => $attributeOptionUiBehavior,
            ]);
        }
    }
}
