<?php


final class SignInMethod {
    const RFID = 0;
    const WIFI = 1;
    const ADMIN = 2;
}

final class MembershipType {
    const
        MONTHLY   = 0
        , DAILY   = 1
        , PAY_IN_PERSON = 2
    ;
}

final class MembershipStatus {
    const
        APPLICANT_AWAITING_APPROVAL = 1
        , APPLICANT_DENIED = 2
        , APPLICANT_APPROVED = 3
        , ACTIVE_MEMBER = 4
        , INACTIVE_MEMBER = 5
    ;
}

final class EmailTemplates {
    const
        APPLICATION_CONFIRMATION = 1
        , APPLICATION_APPROVAL = 2
        , MEMBERSHIP_INVITATION = 3
    ;
}

final class UserIdType {
    const
        ID            = 0
        , RFID        = 1
        , WORDPRESSID = 2
        , WORDPRESSLOGIN=3
    ;
}

final class PricingPeriod {
    const
        FLAT        = 0
        , HOURLY    = 1
        , DAILY     = 2
        , MONTHLY   = 3
    ;
}

final class TransparentPost {
    const
        BILLING        = 0
        , SUBSCRIPTION    = 1
        , TRANSACTION     = 2
    ;
}

final class ProductType {
    const
        SPACE       = 0
        , RESOURCE  = 1
        , LOCATION  = 2
    ;
}

final class MemberIssueType {
    const
        GENERAL         = 0
        , SIGNIN        = 1
        , BILLING       = 2
        , SYNC          = 3
    ;
}
final class UserRoleType {
    const
        SUBSCRIBER      = 0
        , EDITOR        = 1
        , ADMINISTRATOR = 2
    ;
}
?>