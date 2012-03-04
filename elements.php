<?php
/**
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package DublinCoreExtended
 */

// Dublin Core properties and property refinements See: http://dublincore.org/documents/dcmi-terms/
$elements = array(
    array(
        'label' => 'Title',
        'name'  => 'title'
    ),
    array(
        'label'       => __('Alternative Title'),
        'name'        => 'alternative',
        'description' => __('An alternative name for the resource. The distinction between titles and alternative titles is application-specific.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Title'
    ),
    array(
        'label' => 'Subject',
        'name'  => 'subject'
    ),
    array(
        'label' => 'Description',
        'name'  => 'description'
    ),
    array(
        'label'       => __('Abstract'),
        'name'        => 'abstract',
        'description' => __('A summary of the resource.'),
        'data_type'   => 'Text',
        '_refines' => 'Description'
    ),
    array(
        'label'       => __('Table Of Contents'),
        'name'        => 'tableOfContents',
        'description' => __('A list of subunits of the resource.'),
        'data_type'   => 'Text',
        '_refines' => 'Description'
    ),
    array(
        'label' => 'Creator',
        'name'  => 'creator'
    ),
    array(
        'label' => 'Source',
        'name'  => 'source'
    ),
    array(
        'label' => 'Publisher',
        'name'  => 'publisher'
    ),
    array(
        'label' => 'Date',
        'name'  => 'date'
    ),
    array(
        'label'       => __('Date Available'),
        'name'        => 'available',
        'description' => __('Date (often a range) that the resource became or will become available.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Date'
    ),
    array(
        'label'       => __('Date Created'),
        'name'        => 'created',
        'description' => __('Date of creation of the resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Date'
    ),
    array(
        'label'       => __('Date Accepted'),
        'name'        => 'dateAccepted',
        'description' => __('Date of acceptance of the resource. Examples of resources to which a Date Accepted may be relevant are a thesis (accepted by a university department) or an article (accepted by a journal).'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Date'
    ),
    array(
        'label'       => __('Date Copyrighted'),
        'name'        => 'dateCopyrighted',
        'description' => __('Date of copyright.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Date'
    ),
    array(
        'label'       => __('Date Submitted'),
        'name'        => 'dateSubmitted',
        'description' => __('Date of submission of the resource. Examples of resources to which a Date Submitted may be relevant are a thesis (submitted to a university department) or an article (submitted to a journal).'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Date'
    ),
    array(
        'label'       => __('Date Issued'),
        'name'        => 'issued',
        'description' => __('Date of formal issuance (e.g., publication) of the resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Date'
    ),
    array(
        'label'        => __('Date Modified'),
        'name'        => '',
        'description' => __('Date on which the resource was changed.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Date'
    ),
    array(
        'label'       => __('Date Valid'),
        'name'        => 'valid',
        'description' => __('Date (often a range) of validity of a resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Date'
    ),
    array(
        'label' => 'Contributor',
        'name'  => 'contributor'
    ),
    array(
        'label' => 'Rights',
        'name'  => 'rights'
    ),
    array(
        'label'       => __('Access Rights'),
        'name'        => 'accessRights',
        'description' => __('Information about who can access the resource or an indication of its security status. Access Rights may include information regarding access or restrictions based on privacy, security, or other policies.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Rights'
    ),
    array(
        'label'       => __('License'),
        'name'        => 'license',
        'description' => __('A legal document giving official permission to do something with the resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Rights'
    ),
    array(
        'label' => 'Relation',
        'name'  => 'relation'
    ),
    array(
        'label'       => __('Conforms To'),
        'name'        => 'conformsTo',
        'description' => __('An established standard to which the described resource conforms.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Has Format'),
        'name'        => 'hasFormat',
        'description' => __('A related resource that is substantially the same as the pre-existing described resource, but in another format.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Has Part'),
        'name'        => 'hasPart',
        'description' => __('A related resource that is included either physically or logically in the described resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Has Version'),
        'name'        => 'hasVersion',
        'description' => __('A related resource that is a version, edition, or adaptation of the described resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Is Format Of'),
        'name'        => 'isFormatOf',
        'description' => __('A related resource that is substantially the same as the described resource, but in another format.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Is Part Of'),
        'name'        => 'isPartOf',
        'description' => __('A related resource in which the described resource is physically or logically included.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Is Referenced By'),
        'name'        => 'isReferencedBy',
        'description' => __('A related resource that references, cites, or otherwise points to the described resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Is Replaced By'),
        'name'        => 'isReplacedBy',
        'description' => __('A related resource that supplants, displaces, or supersedes the described resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Is Required By'),
        'name'        => 'isRequiredBy',
        'description' => __('A related resource that requires the described resource to support its function, delivery, or coherence.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Is Version Of'),
        'name'        => 'isVersionOf',
        'description' => __('A related resource of which the described resource is a version, edition, or adaptation. Changes in version imply substantive changes in content rather than differences in format.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('References'),
        'name'        => 'references',
        'description' => __('A related resource that is referenced, cited, or otherwise pointed to by the described resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Replaces'),
        'name'        => 'replaces',
        'description' => __('A related resource that is supplanted, displaced, or superseded by the described resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label'       => __('Requires'),
        'name'        => 'requires',
        'description' => __('A related resource that is required by the described resource to support its function, delivery, or coherence.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Relation'
    ),
    array(
        'label' => 'Format',
        'name'  => 'format'
    ),
    array(
        'label'       => __('Extent'),
        'name'        => 'extent',
        'description' => __('The size or duration of the resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Format'
    ),
    array(
        'label'       => __('Medium'),
        'name'        => 'medium',
        'description' => __('The material or physical carrier of the resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Format'
    ),
    array(
        'label' => 'Language',
        'name'  => 'language'
    ),
    array(
        'label' => 'Type',
        'name'  => 'type'
    ),
    array(
        'label' => 'Identifier',
        'name'  => 'identifier'
    ),
    array(
        'label'       => __('Bibliographic Citation'),
        'name'        => 'bibliographicCitation',
        'description' => __('A bibliographic reference for the resource. Recommended practice is to include sufficient bibliographic detail to identify the resource as unambiguously as possible.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Identifier'
    ),
    array(
        'label' => 'Coverage',
        'name'  => 'coverage'
    ),
    array(
        'label'       => __('Spatial Coverage'),
        'name'        => 'spatial',
        'description' => __('Spatial characteristics of the resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Coverage'
    ),
    array(
        'label'       => __('Temporal Coverage'),
        'name'        => 'temporal',
        'description' => __('Temporal characteristics of the resource.'),
        'data_type'   => 'Tiny Text',
        '_refines' => 'Coverage'
    ),
    array(
        'label'       => __('Accrual Method'),
        'name'        => 'accrualMethod',
        'description' => __('The method by which items are added to a collection.'),
        'data_type'   => 'Tiny Text'
    ),
    array(
        'label'       => __('Accrual Periodicity'),
        'name'        => 'accrualPeriodicity',
        'description' => __('The frequency with which items are added to a collection.'),
        'data_type'   => 'Tiny Text'
    ),
    array(
        'label'       => __('Accrual Policy'),
        'name'        => 'accrualPolicy',
        'description' => __('The policy governing the addition of items to a collection.'),
        'data_type'   => 'Tiny Text'
    ),
    array(
        'label'       => __('Audience'),
        'name'        => 'audience',
        'description' => __('A class of entity for whom the resource is intended or useful.'),
        'data_type'   => 'Tiny Text'
    ),
    array(
        'label'       => __('Audience Education Level'),
        'name'        => 'educationLevel',
        'description' => __('A class of entity, defined in terms of progression through an educational or training context, for which the described resource is intended.'),
        'data_type'   => 'Tiny Text',
        '_refines' => __('Audience')
    ),
    array(
        'label'       => __('Mediator'),
        'name'        => 'mediator',
        'description' => __('An entity that mediates access to the resource and for whom the resource is intended or useful. In an educational context, a mediator might be a parent, teacher, teaching assistant, or care-giver.'),
        'data_type'   => 'Tiny Text',
        '_refines' => __('Audience')
    ),
    array(
        'label'       => __('Instructional Method'),
        'name'        => 'instructionalMethod',
        'description' => __('A process, used to engender knowledge, attitudes and skills, that the described resource is designed to support. Instructional Method will typically include ways of presenting instructional materials or conducting instructional activities, patterns of learner-to-learner and learner-to-instructor interactions, and mechanisms by which group and individual levels of learning are measured. Instructional methods include all aspects of the instruction and learning processes from planning and implementation through evaluation and feedback.'),
        'data_type'   => 'Tiny Text'
    ),
    array(
        'label'       => __('Provenance'),
        'name'        => 'provenance',
        'description' => __('A statement of any changes in ownership and custody of the resource since its creation that are significant for its authenticity, integrity, and interpretation. The statement may include a description of any changes successive custodians made to the resource.'),
        'data_type'   => 'Tiny Text'
    ),
    array(
        'label'       => __('Rights Holder'),
        'name'        => 'rightsHolder',
        'description' => __('A person or organization owning or managing rights over the resource.'),
        'data_type'   => 'Tiny Text'
    )
);

