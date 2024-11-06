<?php

namespace Database\Seeders;

use App\Models\CalculatorItem;
use Illuminate\Database\Seeder;

class RefinanceLoanCalculatorItem extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $refinance_items = [
		    [
                'cid' => 5,
                'section' => 'professional_charges',
                'type_of_price' => 'property_legal_fee',
                'name' => 'facility_agreement',
                'label' => 'Facility Agreement',
                'price' => null,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'professional_charges',
                'type_of_price' => 'min_pp',
                'name' => 'memo_charge',
                'label' => 'Memorandum of Charge',
                'price' => null,
                'subsequent_price' => null,
                'percentage' => 10,
                'min_price' => 300,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'professional_charges',
                'type_of_price' => 'fix_price',
                'name' => 'entry_withdrawal_caveat',
                'label' => 'Entry & Withdrawal of Caveat',
                'price' => 350.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'professional_charges',
                'type_of_price' => 'fix_price',
                'name' => 'statutory_declaration',
                'label' => 'Statutory Declaration',
                'price' => 100.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'reimbursements',
                'type_of_price' => 'fix_price',
                'name' => 'affirmation_sd_fee',
                'label' => 'Affirmation Fee for Statutory Declaration',
                'price' => 40.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'reimbursements',
                'type_of_price' => 'fix_price',
                'name' => 'travelling_despatch',
                'label' => 'Travelling & Despatch',
                'price' => 150.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'reimbursements',
                'type_of_price' => 'fix_price',
                'name' => 'courier_postage',
                'label' => 'Courier & Postage',
                'price' => 150.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'reimbursements',
                'type_of_price' => 'fix_price',
                'name' => 'printing_stationery',
                'label' => 'Printing and Stationery',
                'price' => 150.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'reimbursements',
                'type_of_price' => 'fix_price',
                'name' => 'misc',
                'label' => 'Miscellaneous',
                'price' => 100.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'reimbursements',
                'type_of_price' => 'fix_price',
                'name' => 'purchase_document_fees',
                'label' => 'Purchase Document Fees',
                'price' => 350.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'property_legal_fee',
                'name' => 'stamp_duty_facility_agreement',
                'label' => 'Stamp Duty of Facility Agreement (FA)',
                'price' => null,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'sd_dfa',
                'label' => 'Stamp Duty on Duplicate FA - 2 copies',
                'price' => 20.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'sd_letter_offer',
                'label' => 'Stamp Duty on Letter of Offer',
                'price' => 30.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'sd_charge_annexures',
                'label' => 'Stamp Duty on Charge & Annexures',
                'price' => 40.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'sup_letter_offer',
                'label' => 'Supplementary Letter of Offer',
                'price' => 0.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'sd_statutory_declaration',
                'label' => 'Stamp Duty on Statutory Declaration',
                'price' => 20.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
			[
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'stamp_duty_on_discharge',
                'label' => 'Stamp Duty on Discharge of Charge',
                'price' => 10.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'land_search',
                'label' => 'Land Search / Officeal Land Search',
                'price' => 240.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
			[
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'registration_fee_on_discharge',
                'label' => 'Registration Fee on Discharge of Charge',
                'price' => 120.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'reg_charge',
                'label' => 'Registration of Charge',
                'price' => 120.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'fix_price',
                'name' => 'reg_entry_withdrawal',
                'label' => 'Registration of Entry and Withdrawal/Caveator\'s Consent',
                'price' => 210.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'disbursements',
                'type_of_price' => 'property_legal_fee',
                'name' => 'other_fees',
                'label' => 'Bankruptcy / Winding-up Search',
                'price' => null,
                'subsequent_price' => 30,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'cid' => 5,
                'section' => 'professional_charges',
                'type_of_price' => 'fix_price',
                'name' => 'cost_of_discharge',
                'label' => 'Cost of Discharge of Charge',
                'price' => 400.00,
                'subsequent_price' => null,
                'percentage' => null,
                'min_price' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
		];
		
		foreach ($refinance_items as $i => $value) {
            $refinance_items[$i]["pos"] = $i + 1;
        }
		
		CalculatorItem::insert($refinance_items);
    }
}
