<?php

class PartyUserRolesTableSeederTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('partyUserRoles')->delete();

        $partyuserroles = array(
            array(
                'name' => 'Owner',
                'canCloseParty' => true,
                'canSendNotification' => true,
                'canEditPeople' => true,
                'canDeletePeople' => true,
                'canDeleteOthersPost' => true
            ),
            array(
                'name' => 'Hoster',
                'canCloseParty' => false,
                'canSendNotification' => true,
                'canEditPeople' => true,
                'canDeletePeople' => true,
                'canDeleteOthersPost' => true
            ),
            array(
                'name' => 'Officer',
                'canCloseParty' => false,
                'canSendNotification' => false,
                'canEditPeople' => true,
                'canDeletePeople' => true,
                'canDeleteOthersPost' => true
            ),
            array(
                'name' => 'Member',
                'canCloseParty' => false,
                'canSendNotification' => false,
                'canEditPeople' => false,
                'canDeletePeople' => false,
                'canDeleteOthersPost' => false
            )
        );

        // Uncomment the below to run the seeder
        DB::table('partyUserRoles')->insert($partyuserroles);
    }

}