<?php
// Dublin Core properties and property refinements See: http://dublincore.org/documents/dcmi-terms/
$elements = array(
    array(
        'name'=> 'Title'
    ), 
    array(
        'name'        => 'Alternative Title', 
        'description' => 'An alternative name for the resource. The distinction between titles and alternative titles is application-specific.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Title'
    ), 
    array(
        'name'=> 'Subject'
    ), 
    array(
        'name'=> 'Description'
    ), 
    array(
        'name'        => 'Abstract', 
        'description' => 'A summary of the resource.', 
        'data_type'   => 'Text', 
        '_refines'    => 'Description'
    ), 
    array(
        'name'        => 'Table Of Contents', 
        'description' => 'A list of subunits of the resource.', 
        'data_type'   => 'Text', 
        '_refines'    => 'Description'
    ), 
    array(
        'name'=> 'Creator'
    ), 
    array(
        'name'=> 'Source'
    ), 
    array(
        'name'=> 'Publisher'
    ), 
    array(
        'name'=> 'Date'
    ), 
    array(
        'name'        => 'Date Available', 
        'description' => 'Date (often a range) that the resource became or will become available.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Date'
    ), 
    array(
        'name'        => 'Date Created', 
        'description' => 'Date of creation of the resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Date'
    ), 
    array(
        'name'        => 'Date Accepted', 
        'description' => 'Date of acceptance of the resource. Examples of resources to which a Date Accepted may be relevant are a thesis (accepted by a university department) or an article (accepted by a journal).', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Date'
    ), 
    array(
        'name'        => 'Date Copyrighted', 
        'description' => 'Date of copyright.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Date'
    ), 
    array(
        'name'        => 'Date Submitted', 
        'description' => 'Date of submission of the resource. Examples of resources to which a Date Submitted may be relevant are a thesis (submitted to a university department) or an article (submitted to a journal).', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Date'
    ), 
    array(
        'name'        => 'Date Issued', 
        'description' => 'Date of formal issuance (e.g., publication) of the resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Date'
    ), 
    array(
        'name'        => 'Date Modified', 
        'description' => 'Date on which the resource was changed.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Date'
    ), 
    array(
        'name'        => 'Date Valid', 
        'description' => 'Date (often a range) of validity of a resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Date'
    ), 
    array(
        'name'=> 'Contributor'
    ), 
    array(
        'name'=> 'Rights'
    ), 
    array(
        'name'        => 'Access Rights', 
        'description' => 'Information about who can access the resource or an indication of its security status. Access Rights may include information regarding access or restrictions based on privacy, security, or other policies.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Rights'
    ), 
    array(
        'name'        => 'License', 
        'description' => 'A legal document giving official permission to do something with the resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Rights'
    ), 
    array(
        'name'=> 'Relation'
    ), 
    array(
        'name'        => 'Conforms To', 
        'description' => 'An established standard to which the described resource conforms.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Has Format', 
        'description' => 'A related resource that is substantially the same as the pre-existing described resource, but in another format.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Has Part', 
        'description' => 'A related resource that is included either physically or logically in the described resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Has Version', 
        'description' => 'A related resource that is a version, edition, or adaptation of the described resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Is Format Of', 
        'description' => 'A related resource that is substantially the same as the described resource, but in another format.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Is Part Of', 
        'description' => 'A related resource in which the described resource is physically or logically included.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Is Referenced By', 
        'description' => 'A related resource that references, cites, or otherwise points to the described resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Is Replaced By', 
        'description' => 'A related resource that supplants, displaces, or supersedes the described resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Is Required By', 
        'description' => 'A related resource that requires the described resource to support its function, delivery, or coherence.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Is Version Of', 
        'description' => 'A related resource of which the described resource is a version, edition, or adaptation. Changes in version imply substantive changes in content rather than differences in format.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'References', 
        'description' => 'A related resource that is referenced, cited, or otherwise pointed to by the described resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Replaces', 
        'description' => 'A related resource that is supplanted, displaced, or superseded by the described resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'        => 'Requires', 
        'description' => 'A related resource that is required by the described resource to support its function, delivery, or coherence.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Relation'
    ), 
    array(
        'name'=> 'Format'
    ), 
    array(
        'name'        => 'Extent', 
        'description' => 'The size or duration of the resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Format'
    ), 
    array(
        'name'        => 'Medium', 
        'description' => 'The material or physical carrier of the resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Format'
    ), 
    array(
        'name'=> 'Language'
    ), 
    array(
        'name'=> 'Type'
    ), 
    array(
        'name'=> 'Identifier'
    ), 
    array(
        'name'        => 'Bibliographic Citation', 
        'description' => 'A bibliographic reference for the resource. Recommended practice is to include sufficient bibliographic detail to identify the resource as unambiguously as possible.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Identifier'
    ), 
    array(
        'name'=> 'Coverage'
    ), 
    array(
        'name'        => 'Spatial Coverage', 
        'description' => 'Spatial characteristics of the resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Coverage'
    ), 
    array(
        'name'        => 'Temporal Coverage', 
        'description' => 'Temporal characteristics of the resource.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Coverage'
    ), 
    array(
        'name'        => 'Accrual Method', 
        'description' => 'The method by which items are added to a collection.', 
        'data_type'   => 'Tiny Text'
    ), 
    array(
        'name'        => 'Accrual Periodicity', 
        'description' => 'The frequency with which items are added to a collection.', 
        'data_type'   => 'Tiny Text'
    ), 
    array(
        'name'        => 'Accrual Policy', 
        'description' => 'The policy governing the addition of items to a collection.', 
        'data_type'   => 'Tiny Text'
    ), 
    array(
        'name'        => 'Audience', 
        'description' => 'A class of entity for whom the resource is intended or useful.', 
        'data_type'   => 'Tiny Text'
    ), 
    array(
        'name'        => 'Audience Education Level', 
        'description' => 'A class of entity, defined in terms of progression through an educational or training context, for which the described resource is intended.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Audience'
    ), 
    array(
        'name'        => 'Mediator', 
        'description' => 'An entity that mediates access to the resource and for whom the resource is intended or useful. In an educational context, a mediator might be a parent, teacher, teaching assistant, or care-giver.', 
        'data_type'   => 'Tiny Text', 
        '_refines'    => 'Audience'
    ), 
    array(
        'name'        => 'Instructional Method', 
        'description' => 'A process, used to engender knowledge, attitudes and skills, that the described resource is designed to support. Instructional Method will typically include ways of presenting instructional materials or conducting instructional activities, patterns of learner-to-learner and learner-to-instructor interactions, and mechanisms by which group and individual levels of learning are measured. Instructional methods include all aspects of the instruction and learning processes from planning and implementation through evaluation and feedback.', 
        'data_type'   => 'Tiny Text'
    ), 
    array(
        'name'        => 'Provenance', 
        'description' => 'A statement of any changes in ownership and custody of the resource since its creation that are significant for its authenticity, integrity, and interpretation. The statement may include a description of any changes successive custodians made to the resource.', 
        'data_type'   => 'Tiny Text'
    ), 
    array(
        'name'        => 'Rights Holder', 
        'description' => 'A person or organization owning or managing rights over the resource.', 
        'data_type'   => 'Tiny Text'
    )
);