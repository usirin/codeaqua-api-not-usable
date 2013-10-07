<?php

class GroupRolesTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('groupRoles')->delete();

        $grouprole = array(
            array(
                'name' => 'Owner',
                'canEditPage' => true,
                'canClosePage' => true,
                'canCreateParty' => true,
                'canDeleteParty' => true,
                'canDeleteWallPost' => true,
                'canDeleteWallComment' => true,
                'canSeeRequest' => true,
                'canInvitePeople' => true,
                'canEditMember' => true,
                'canDeleteMember' => true
            ),
            array(
                'name' => 'Officer',
                'canEditPage' => true,
                'canClosePage' => false,
                'canCreateParty' => true,
                'canDeleteParty' => true,
                'canDeleteWallPost' => true,
                'canDeleteWallComment' => true,
                'canSeeRequest' => true,
                'canInvitePeople' => true,
                'canEditMember' => true,
                'canDeleteMember' => true
            ),
            array(
                'name' => 'Member',
                'canEditPage' => false,
                'canClosePage' => false,
                'canCreateParty' => true,
                'canDeleteParty' => true,
                'canDeleteWallPost' => false,
                'canDeleteWallComment' => false,
                'canSeeRequest' => false,
                'canInvitePeople' => true,
                'canEditMember' => false,
                'canDeleteMember' => false
            )
        );

        // Uncomment the below to run the seeder
        DB::table('groupRoles')->insert($grouprole);
    }

}