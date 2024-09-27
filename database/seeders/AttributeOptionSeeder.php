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
            'g',
            'kg',
            'oz',
            'lb',
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
            'Celsius (C)',
            'Fahrenheit (F)',
        ]);

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
            __('Once'),
            __('Once daily (sid)'),
            __('2 times daily (bid)'),
            __('3 times daily (tid)'),
            __('4 times daily (qid)'),
            __('5 times daily (5xd)'),
            __('6 times daily (6xd)'),
            __('Every 2 days (q2d)'),
            __('Every 3 days (q3d)'),
            __('Every 4 days (q4d)'),
            __('Every 5 days (q5d)'),
            __('Once a week (q7d)'),
            __('Every 2 weeks (q14d)'),
            __('Every 3 weeks (q21d)'),
            __('Every 4 weeks (q28d)'),
            __('Before eating (ac)'),
            __('After eating (pc)'),
            __('Every morning (om)'),
            __('Every evening (on)'),
            __('As Needed (prn)'),
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
            __('Gavage'),
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
    }

    private function createOrUpdateAttributes(AttributeOptionName $attributeName, array $values): void
    {
        foreach ($values as $index => $value) {
            AttributeOption::firstOrNew([
                'name' => $attributeName,
                'value' => $value,
            ])->fill([
                'value_lowercase' => strtolower($value),
                'sort_order' => $index + 1,
            ])->save();
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
