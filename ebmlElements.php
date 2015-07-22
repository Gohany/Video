<?php

// Information needed to parse all possible element types in a document
class EBMLElements
{

        public $elements = [
            'a45dfa3' => [
                'datatype' => 'container',
                'name' => 'EBML',
                'validParents' => [
                    'root',
                ],
            ],
            '286' => [
                'datatype' => 'uint',
                'name' => 'EBMLVersion',
                'validParents' => [
                    '172351395',
                ],
            ],
            '2f7' => [
                'datatype' => 'uint',
                'name' => 'EBMLReadVersion',
                'validParents' => [
                    '172351395',
                ],
            ],
            '2f2' => [
                'datatype' => 'uint',
                'name' => 'EBMLMaxIDLength',
                'validParents' => [
                    '172351395',
                ],
            ],
            '2f3' => [
                'datatype' => 'uint',
                'name' => 'EBMLMaxSizeLength',
                'validParents' => [
                    '172351395',
                ],
            ],
            '282' => [
                'datatype' => 'string',
                'name' => 'DocType',
                'validParents' => [
                    '172351395',
                ],
            ],
            '287' => [
                'datatype' => 'uint',
                'name' => 'DocTypeVersion',
                'validParents' => [
                    '172351395',
                ],
            ],
            '285' => [
                'datatype' => 'uint',
                'name' => 'DocTypeReadVersion',
                'validParents' => [
                    '172351395',
                ],
            ],
            '6c' => [
                'datatype' => 'binary',
                'name' => 'Void',
                'validParents' => [
                    '*',
                ],
            ],
            '3f' => [
                'datatype' => 'binary',
                'name' => 'CRC-32',
                'validParents' => [
                    '*',
                ],
            ],
            'b538667' => [
                'datatype' => 'container',
                'name' => 'SignatureSlot',
                'validParents' => [
                    '*',
                ],
            ],
            '3e8a' => [
                'datatype' => 'uint',
                'name' => 'SignatureAlgo',
                'validParents' => [
                    '190023271',
                ],
            ],
            '3e9a' => [
                'datatype' => 'uint',
                'name' => 'SignatureHash',
                'validParents' => [
                    '190023271',
                ],
            ],
            '3ea5' => [
                'datatype' => 'binary',
                'name' => 'SignaturePublicKey',
                'validParents' => [
                    '190023271',
                ],
            ],
            '3eb5' => [
                'datatype' => 'binary',
                'name' => 'Signature',
                'validParents' => [
                    '190023271',
                ],
            ],
            '3e5b' => [
                'datatype' => 'container',
                'name' => 'SignatureElements',
                'validParents' => [
                    '190023271',
                ],
            ],
            '3e7b' => [
                'datatype' => 'container',
                'name' => 'SignatureElementList',
                'validParents' => [
                    '15963',
                ],
            ],
            '2532' => [
                'datatype' => 'binary',
                'name' => 'SignedElement',
                'validParents' => [
                    '15995',
                ],
            ],
            '8538067' => [
                'datatype' => 'container',
                'name' => 'Segment',
                'validParents' => [
                    'root',
                ],
            ],
            '14d9b74' => [
                'datatype' => 'container',
                'name' => 'SeekHead',
                'validParents' => [
                    '139690087',
                ],
            ],
            'dbb' => [
                'datatype' => 'container',
                'name' => 'Seek',
                'validParents' => [
                    '21863284',
                ],
            ],
            '13ab' => [
                'datatype' => 'binary',
                'name' => 'SeekID',
                'validParents' => [
                    '3515',
                ],
            ],
            '13ac' => [
                'datatype' => 'uint',
                'name' => 'SeekPosition',
                'validParents' => [
                    '3515',
                ],
            ],
            '549a966' => [
                'datatype' => 'container',
                'name' => 'Info',
                'validParents' => [
                    '139690087',
                ],
            ],
            '33a4' => [
                'datatype' => 'binary',
                'name' => 'SegmentUID',
                'validParents' => [
                    '88713574',
                ],
            ],
            '3384' => [
                'datatype' => 'string',
                'name' => 'SegmentFilename',
                'validParents' => [
                    '88713574',
                ],
            ],
            '1cb923' => [
                'datatype' => 'binary',
                'name' => 'PrevUID',
                'validParents' => [
                    '88713574',
                ],
            ],
            '1c83ab' => [
                'datatype' => 'string',
                'name' => 'PrevFilename',
                'validParents' => [
                    '88713574',
                ],
            ],
            '1eb923' => [
                'datatype' => 'binary',
                'name' => 'NextUID',
                'validParents' => [
                    '88713574',
                ],
            ],
            '1e83bb' => [
                'datatype' => 'string',
                'name' => 'NextFilename',
                'validParents' => [
                    '88713574',
                ],
            ],
            '444' => [
                'datatype' => 'binary',
                'name' => 'SegmentFamily',
                'validParents' => [
                    '88713574',
                ],
            ],
            '2924' => [
                'datatype' => 'container',
                'name' => 'ChapterTranslate',
                'validParents' => [
                    '88713574',
                ],
            ],
            '29fc' => [
                'datatype' => 'uint',
                'name' => 'ChapterTranslateEditionUID',
                'validParents' => [
                    '10532',
                ],
            ],
            '29bf' => [
                'datatype' => 'uint',
                'name' => 'ChapterTranslateCodec',
                'validParents' => [
                    '10532',
                ],
            ],
            '29a5' => [
                'datatype' => 'binary',
                'name' => 'ChapterTranslateID',
                'validParents' => [
                    '10532',
                ],
            ],
            'ad7b1' => [
                'datatype' => 'uint',
                'name' => 'TimecodeScale',
                'validParents' => [
                    '88713574',
                ],
            ],
            '489' => [
                'datatype' => 'float',
                'name' => 'Duration',
                'validParents' => [
                    '88713574',
                ],
            ],
            '461' => [
                'datatype' => 'date',
                'name' => 'DateUTC',
                'validParents' => [
                    '88713574',
                ],
            ],
            '3ba9' => [
                'datatype' => 'string',
                'name' => 'Title',
                'validParents' => [
                    '88713574',
                ],
            ],
            'd80' => [
                'datatype' => 'string',
                'name' => 'MuxingApp',
                'validParents' => [
                    '88713574',
                ],
            ],
            '1741' => [
                'datatype' => 'string',
                'name' => 'WritingApp',
                'validParents' => [
                    '88713574',
                ],
            ],
            'f43b675' => [
                'datatype' => 'container',
                'name' => 'Cluster',
                'validParents' => [
                    '139690087',
                ],
            ],
            '67' => [
                'datatype' => 'uint',
                'name' => 'Timecode',
                'validParents' => [
                    '256095861',
                ],
            ],
            '1854' => [
                'datatype' => 'container',
                'name' => 'SilentTracks',
                'validParents' => [
                    '256095861',
                ],
            ],
            '18d7' => [
                'datatype' => 'uint',
                'name' => 'SilentTrackNumber',
                'validParents' => [
                    '6228',
                ],
            ],
            '27' => [
                'datatype' => 'uint',
                'name' => 'Position',
                'validParents' => [
                    '256095861',
                ],
            ],
            '2b' => [
                'datatype' => 'uint',
                'name' => 'PrevSize',
                'validParents' => [
                    '256095861',
                ],
            ],
            '23' => [
                'datatype' => 'binary',
                'name' => 'SimpleBlock',
                'validParents' => [
                    '256095861',
                ],
            ],
            '20' => [
                'datatype' => 'container',
                'name' => 'BlockGroup',
                'validParents' => [
                    '256095861',
                ],
            ],
            '21' => [
                'datatype' => 'binary',
                'name' => 'Block',
                'validParents' => [
                    '32',
                ],
            ],
            '22' => [
                'datatype' => 'binary',
                'name' => 'BlockVirtual',
                'validParents' => [
                    '32',
                ],
            ],
            '35a1' => [
                'datatype' => 'container',
                'name' => 'BlockAdditions',
                'validParents' => [
                    '32',
                ],
            ],
            '26' => [
                'datatype' => 'container',
                'name' => 'BlockMore',
                'validParents' => [
                    '13729',
                ],
            ],
            '6e' => [
                'datatype' => 'uint',
                'name' => 'BlockAddID',
                'validParents' => [
                    '38',
                ],
            ],
            '25' => [
                'datatype' => 'binary',
                'name' => 'BlockAdditional',
                'validParents' => [
                    '38',
                ],
            ],
            '1b' => [
                'datatype' => 'uint',
                'name' => 'BlockDuration',
                'validParents' => [
                    '32',
                ],
            ],
            '7a' => [
                'datatype' => 'uint',
                'name' => 'ReferencePriority',
                'validParents' => [
                    '32',
                ],
            ],
            '7b' => [
                'datatype' => 'int',
                'name' => 'ReferenceBlock',
                'validParents' => [
                    '32',
                ],
            ],
            '7d' => [
                'datatype' => 'int',
                'name' => 'ReferenceVirtual',
                'validParents' => [
                    '32',
                ],
            ],
            '24' => [
                'datatype' => 'binary',
                'name' => 'CodecState',
                'validParents' => [
                    '32',
                ],
            ],
            '35a2' => [
                'datatype' => 'int',
                'name' => 'DiscardPadding',
                'validParents' => [
                    '32',
                ],
            ],
            'e' => [
                'datatype' => 'container',
                'name' => 'Slices',
                'validParents' => [
                    '32',
                ],
            ],
            '68' => [
                'datatype' => 'container',
                'name' => 'TimeSlice',
                'validParents' => [
                    '14',
                ],
            ],
            '4c' => [
                'datatype' => 'uint',
                'name' => 'LaceNumber',
                'validParents' => [
                    '104',
                ],
            ],
            '4d' => [
                'datatype' => 'uint',
                'name' => 'FrameNumber',
                'validParents' => [
                    '104',
                ],
            ],
            '4b' => [
                'datatype' => 'uint',
                'name' => 'BlockAdditionID',
                'validParents' => [
                    '104',
                ],
            ],
            '4e' => [
                'datatype' => 'uint',
                'name' => 'Delay',
                'validParents' => [
                    '104',
                ],
            ],
            '4f' => [
                'datatype' => 'uint',
                'name' => 'SliceDuration',
                'validParents' => [
                    '104',
                ],
            ],
            '48' => [
                'datatype' => 'container',
                'name' => 'ReferenceFrame',
                'validParents' => [
                    '32',
                ],
            ],
            '49' => [
                'datatype' => 'uint',
                'name' => 'ReferenceOffset',
                'validParents' => [
                    '72',
                ],
            ],
            '4a' => [
                'datatype' => 'uint',
                'name' => 'ReferenceTimeCode',
                'validParents' => [
                    '72',
                ],
            ],
            '2f' => [
                'datatype' => 'binary',
                'name' => 'EncryptedBlock',
                'validParents' => [
                    '256095861',
                ],
            ],
            '654ae6b' => [
                'datatype' => 'container',
                'name' => 'Tracks',
                'validParents' => [
                    '139690087',
                ],
            ],
            '2e' => [
                'datatype' => 'container',
                'name' => 'TrackEntry',
                'validParents' => [
                    '106212971',
                ],
            ],
            '57' => [
                'datatype' => 'uint',
                'name' => 'TrackNumber',
                'validParents' => [
                    '46',
                ],
            ],
            '33c5' => [
                'datatype' => 'uint',
                'name' => 'TrackUID',
                'validParents' => [
                    '46',
                ],
            ],
            '3' => [
                'datatype' => 'uint',
                'name' => 'TrackType',
                'validParents' => [
                    '46',
                ],
            ],
            '39' => [
                'datatype' => 'uint',
                'name' => 'FlagEnabled',
                'validParents' => [
                    '46',
                ],
            ],
            '8' => [
                'datatype' => 'uint',
                'name' => 'FlagDefault',
                'validParents' => [
                    '46',
                ],
            ],
            '15aa' => [
                'datatype' => 'uint',
                'name' => 'FlagForced',
                'validParents' => [
                    '46',
                ],
            ],
            '1c' => [
                'datatype' => 'uint',
                'name' => 'FlagLacing',
                'validParents' => [
                    '46',
                ],
            ],
            '2de7' => [
                'datatype' => 'uint',
                'name' => 'MinCache',
                'validParents' => [
                    '46',
                ],
            ],
            '2df8' => [
                'datatype' => 'uint',
                'name' => 'MaxCache',
                'validParents' => [
                    '46',
                ],
            ],
            '3e383' => [
                'datatype' => 'uint',
                'name' => 'DefaultDuration',
                'validParents' => [
                    '46',
                ],
            ],
            '34e7a' => [
                'datatype' => 'uint',
                'name' => 'DefaultDecodedFieldDuration',
                'validParents' => [
                    '46',
                ],
            ],
            '3314f' => [
                'datatype' => 'float',
                'name' => 'TrackTimecodeScale',
                'validParents' => [
                    '46',
                ],
            ],
            '137f' => [
                'datatype' => 'int',
                'name' => 'TrackOffset',
                'validParents' => [
                    '46',
                ],
            ],
            '15ee' => [
                'datatype' => 'uint',
                'name' => 'MaxBlockAdditionID',
                'validParents' => [
                    '46',
                ],
            ],
            '136e' => [
                'datatype' => 'string',
                'name' => 'Name',
                'validParents' => [
                    '46',
                ],
            ],
            '2b59c' => [
                'datatype' => 'string',
                'name' => 'Language',
                'validParents' => [
                    '46',
                ],
            ],
            '6' => [
                'datatype' => 'string',
                'name' => 'CodecID',
                'validParents' => [
                    '46',
                ],
            ],
            '23a2' => [
                'datatype' => 'binary',
                'name' => 'CodecPrivate',
                'validParents' => [
                    '46',
                ],
            ],
            '58688' => [
                'datatype' => 'string',
                'name' => 'CodecName',
                'validParents' => [
                    '46',
                ],
            ],
            '3446' => [
                'datatype' => 'uint',
                'name' => 'AttachmentLink',
                'validParents' => [
                    '46',
                ],
            ],
            '1a9697' => [
                'datatype' => 'string',
                'name' => 'CodecSettings',
                'validParents' => [
                    '46',
                ],
            ],
            '1b4040' => [
                'datatype' => 'string',
                'name' => 'CodecInfoURL',
                'validParents' => [
                    '46',
                ],
            ],
            '6b240' => [
                'datatype' => 'string',
                'name' => 'CodecDownloadURL',
                'validParents' => [
                    '46',
                ],
            ],
            '2a' => [
                'datatype' => 'uint',
                'name' => 'CodecDecodeAll',
                'validParents' => [
                    '46',
                ],
            ],
            '2fab' => [
                'datatype' => 'uint',
                'name' => 'TrackOverlay',
                'validParents' => [
                    '46',
                ],
            ],
            '16aa' => [
                'datatype' => 'uint',
                'name' => 'CodecDelay',
                'validParents' => [
                    '46',
                ],
            ],
            '16bb' => [
                'datatype' => 'uint',
                'name' => 'SeekPreRoll',
                'validParents' => [
                    '46',
                ],
            ],
            '2624' => [
                'datatype' => 'container',
                'name' => 'TrackTranslate',
                'validParents' => [
                    '46',
                ],
            ],
            '26fc' => [
                'datatype' => 'uint',
                'name' => 'TrackTranslateEditionUID',
                'validParents' => [
                    '9764',
                ],
            ],
            '26bf' => [
                'datatype' => 'uint',
                'name' => 'TrackTranslateCodec',
                'validParents' => [
                    '9764',
                ],
            ],
            '26a5' => [
                'datatype' => 'binary',
                'name' => 'TrackTranslateTrackID',
                'validParents' => [
                    '9764',
                ],
            ],
            '60' => [
                'datatype' => 'container',
                'name' => 'Video',
                'validParents' => [
                    '46',
                ],
            ],
            '1a' => [
                'datatype' => 'uint',
                'name' => 'FlagInterlaced',
                'validParents' => [
                    '96',
                ],
            ],
            '13b8' => [
                'datatype' => 'uint',
                'name' => 'StereoMode',
                'validParents' => [
                    '96',
                ],
            ],
            '13c0' => [
                'datatype' => 'uint',
                'name' => 'AlphaMode',
                'validParents' => [
                    '96',
                ],
            ],
            '13b9' => [
                'datatype' => 'uint',
                'name' => 'OldStereoMode',
                'validParents' => [
                    '96',
                ],
            ],
            '30' => [
                'datatype' => 'uint',
                'name' => 'PixelWidth',
                'validParents' => [
                    '96',
                ],
            ],
            '3a' => [
                'datatype' => 'uint',
                'name' => 'PixelHeight',
                'validParents' => [
                    '96',
                ],
            ],
            '14aa' => [
                'datatype' => 'uint',
                'name' => 'PixelCropBottom',
                'validParents' => [
                    '96',
                ],
            ],
            '14bb' => [
                'datatype' => 'uint',
                'name' => 'PixelCropTop',
                'validParents' => [
                    '96',
                ],
            ],
            '14cc' => [
                'datatype' => 'uint',
                'name' => 'PixelCropLeft',
                'validParents' => [
                    '96',
                ],
            ],
            '14dd' => [
                'datatype' => 'uint',
                'name' => 'PixelCropRight',
                'validParents' => [
                    '96',
                ],
            ],
            '14b0' => [
                'datatype' => 'uint',
                'name' => 'DisplayWidth',
                'validParents' => [
                    '96',
                ],
            ],
            '14ba' => [
                'datatype' => 'uint',
                'name' => 'DisplayHeight',
                'validParents' => [
                    '96',
                ],
            ],
            '14b2' => [
                'datatype' => 'uint',
                'name' => 'DisplayUnit',
                'validParents' => [
                    '96',
                ],
            ],
            '14b3' => [
                'datatype' => 'uint',
                'name' => 'AspectRatioType',
                'validParents' => [
                    '96',
                ],
            ],
            'eb524' => [
                'datatype' => 'binary',
                'name' => 'ColourSpace',
                'validParents' => [
                    '96',
                ],
            ],
            'fb523' => [
                'datatype' => 'float',
                'name' => 'GammaValue',
                'validParents' => [
                    '96',
                ],
            ],
            '383e3' => [
                'datatype' => 'float',
                'name' => 'FrameRate',
                'validParents' => [
                    '96',
                ],
            ],
            '61' => [
                'datatype' => 'container',
                'name' => 'Audio',
                'validParents' => [
                    '46',
                ],
            ],
            '35' => [
                'datatype' => 'float',
                'name' => 'SamplingFrequency',
                'validParents' => [
                    '97',
                ],
            ],
            '38b5' => [
                'datatype' => 'float',
                'name' => 'OutputSamplingFrequency',
                'validParents' => [
                    '97',
                ],
            ],
            '1f' => [
                'datatype' => 'uint',
                'name' => 'Channels',
                'validParents' => [
                    '97',
                ],
            ],
            '3d7b' => [
                'datatype' => 'binary',
                'name' => 'ChannelPositions',
                'validParents' => [
                    '97',
                ],
            ],
            '2264' => [
                'datatype' => 'uint',
                'name' => 'BitDepth',
                'validParents' => [
                    '97',
                ],
            ],
            '62' => [
                'datatype' => 'container',
                'name' => 'TrackOperation',
                'validParents' => [
                    '46',
                ],
            ],
            '63' => [
                'datatype' => 'container',
                'name' => 'TrackCombinePlanes',
                'validParents' => [
                    '98',
                ],
            ],
            '64' => [
                'datatype' => 'container',
                'name' => 'TrackPlane',
                'validParents' => [
                    '99',
                ],
            ],
            '65' => [
                'datatype' => 'uint',
                'name' => 'TrackPlaneUID',
                'validParents' => [
                    '100',
                ],
            ],
            '66' => [
                'datatype' => 'uint',
                'name' => 'TrackPlaneType',
                'validParents' => [
                    '100',
                ],
            ],
            '69' => [
                'datatype' => 'container',
                'name' => 'TrackJoinBlocks',
                'validParents' => [
                    '98',
                ],
            ],
            '6d' => [
                'datatype' => 'uint',
                'name' => 'TrackJoinUID',
                'validParents' => [
                    '105',
                ],
            ],
            '40' => [
                'datatype' => 'uint',
                'name' => 'TrickTrackUID',
                'validParents' => [
                    '46',
                ],
            ],
            '41' => [
                'datatype' => 'binary',
                'name' => 'TrickTrackSegmentUID',
                'validParents' => [
                    '46',
                ],
            ],
            '46' => [
                'datatype' => 'uint',
                'name' => 'TrickTrackFlag',
                'validParents' => [
                    '46',
                ],
            ],
            '47' => [
                'datatype' => 'uint',
                'name' => 'TrickMasterTrackUID',
                'validParents' => [
                    '46',
                ],
            ],
            '44' => [
                'datatype' => 'binary',
                'name' => 'TrickMasterTrackSegmentUID',
                'validParents' => [
                    '46',
                ],
            ],
            '2d80' => [
                'datatype' => 'container',
                'name' => 'ContentEncodings',
                'validParents' => [
                    '46',
                ],
            ],
            '2240' => [
                'datatype' => 'container',
                'name' => 'ContentEncoding',
                'validParents' => [
                    '11648',
                ],
            ],
            '1031' => [
                'datatype' => 'uint',
                'name' => 'ContentEncodingOrder',
                'validParents' => [
                    '8768',
                ],
            ],
            '1032' => [
                'datatype' => 'uint',
                'name' => 'ContentEncodingScope',
                'validParents' => [
                    '8768',
                ],
            ],
            '1033' => [
                'datatype' => 'uint',
                'name' => 'ContentEncodingType',
                'validParents' => [
                    '8768',
                ],
            ],
            '1034' => [
                'datatype' => 'container',
                'name' => 'ContentCompression',
                'validParents' => [
                    '8768',
                ],
            ],
            '254' => [
                'datatype' => 'uint',
                'name' => 'ContentCompAlgo',
                'validParents' => [
                    '4148',
                ],
            ],
            '255' => [
                'datatype' => 'binary',
                'name' => 'ContentCompSettings',
                'validParents' => [
                    '4148',
                ],
            ],
            '1035' => [
                'datatype' => 'container',
                'name' => 'ContentEncryption',
                'validParents' => [
                    '8768',
                ],
            ],
            '7e1' => [
                'datatype' => 'uint',
                'name' => 'ContentEncAlgo',
                'validParents' => [
                    '4149',
                ],
            ],
            '7e2' => [
                'datatype' => 'binary',
                'name' => 'ContentEncKeyID',
                'validParents' => [
                    '4149',
                ],
            ],
            '7e3' => [
                'datatype' => 'binary',
                'name' => 'ContentSignature',
                'validParents' => [
                    '4149',
                ],
            ],
            '7e4' => [
                'datatype' => 'binary',
                'name' => 'ContentSigKeyID',
                'validParents' => [
                    '4149',
                ],
            ],
            '7e5' => [
                'datatype' => 'uint',
                'name' => 'ContentSigAlgo',
                'validParents' => [
                    '4149',
                ],
            ],
            '7e6' => [
                'datatype' => 'uint',
                'name' => 'ContentSigHashAlgo',
                'validParents' => [
                    '4149',
                ],
            ],
            'c53bb6b' => [
                'datatype' => 'container',
                'name' => 'Cues',
                'validParents' => [
                    '139690087',
                ],
            ],
            '3b' => [
                'datatype' => 'container',
                'name' => 'CuePoint',
                'validParents' => [
                    '206814059',
                ],
            ],
            '33' => [
                'datatype' => 'uint',
                'name' => 'CueTime',
                'validParents' => [
                    '59',
                ],
            ],
            '37' => [
                'datatype' => 'container',
                'name' => 'CueTrackPositions',
                'validParents' => [
                    '59',
                ],
            ],
            '77' => [
                'datatype' => 'uint',
                'name' => 'CueTrack',
                'validParents' => [
                    '55',
                ],
            ],
            '71' => [
                'datatype' => 'uint',
                'name' => 'CueClusterPosition',
                'validParents' => [
                    '55',
                ],
            ],
            '70' => [
                'datatype' => 'uint',
                'name' => 'CueRelativePosition',
                'validParents' => [
                    '55',
                ],
            ],
            '32' => [
                'datatype' => 'uint',
                'name' => 'CueDuration',
                'validParents' => [
                    '55',
                ],
            ],
            '1378' => [
                'datatype' => 'uint',
                'name' => 'CueBlockNumber',
                'validParents' => [
                    '55',
                ],
            ],
            '6a' => [
                'datatype' => 'uint',
                'name' => 'CueCodecState',
                'validParents' => [
                    '55',
                ],
            ],
            '5b' => [
                'datatype' => 'container',
                'name' => 'CueReference',
                'validParents' => [
                    '55',
                ],
            ],
            '16' => [
                'datatype' => 'uint',
                'name' => 'CueRefTime',
                'validParents' => [
                    '91',
                ],
            ],
            '17' => [
                'datatype' => 'uint',
                'name' => 'CueRefCluster',
                'validParents' => [
                    '91',
                ],
            ],
            '135f' => [
                'datatype' => 'uint',
                'name' => 'CueRefNumber',
                'validParents' => [
                    '91',
                ],
            ],
            '6b' => [
                'datatype' => 'uint',
                'name' => 'CueRefCodecState',
                'validParents' => [
                    '91',
                ],
            ],
            '941a469' => [
                'datatype' => 'container',
                'name' => 'Attachments',
                'validParents' => [
                    '139690087',
                ],
            ],
            '21a7' => [
                'datatype' => 'container',
                'name' => 'AttachedFile',
                'validParents' => [
                    '155296873',
                ],
            ],
            '67e' => [
                'datatype' => 'string',
                'name' => 'FileDescription',
                'validParents' => [
                    '8615',
                ],
            ],
            '66e' => [
                'datatype' => 'string',
                'name' => 'FileName',
                'validParents' => [
                    '8615',
                ],
            ],
            '660' => [
                'datatype' => 'string',
                'name' => 'FileMimeType',
                'validParents' => [
                    '8615',
                ],
            ],
            '65c' => [
                'datatype' => 'binary',
                'name' => 'FileData',
                'validParents' => [
                    '8615',
                ],
            ],
            '6ae' => [
                'datatype' => 'uint',
                'name' => 'FileUID',
                'validParents' => [
                    '8615',
                ],
            ],
            '675' => [
                'datatype' => 'binary',
                'name' => 'FileReferral',
                'validParents' => [
                    '8615',
                ],
            ],
            '661' => [
                'datatype' => 'uint',
                'name' => 'FileUsedStartTime',
                'validParents' => [
                    '8615',
                ],
            ],
            '662' => [
                'datatype' => 'uint',
                'name' => 'FileUsedEndTime',
                'validParents' => [
                    '8615',
                ],
            ],
            '43a770' => [
                'datatype' => 'container',
                'name' => 'Chapters',
                'validParents' => [
                    '139690087',
                ],
            ],
            '5b9' => [
                'datatype' => 'container',
                'name' => 'EditionEntry',
                'validParents' => [
                    '4433776',
                ],
            ],
            '5bc' => [
                'datatype' => 'uint',
                'name' => 'EditionUID',
                'validParents' => [
                    '1465',
                ],
            ],
            '5bd' => [
                'datatype' => 'uint',
                'name' => 'EditionFlagHidden',
                'validParents' => [
                    '1465',
                ],
            ],
            '5db' => [
                'datatype' => 'uint',
                'name' => 'EditionFlagDefault',
                'validParents' => [
                    '1465',
                ],
            ],
            '5dd' => [
                'datatype' => 'uint',
                'name' => 'EditionFlagOrdered',
                'validParents' => [
                    '1465',
                ],
            ],
            '36' => [
                'datatype' => 'container',
                'name' => 'ChapterAtom',
                'validParents' => [
                    '1465',
                    '54',
                ],
            ],
            '33c4' => [
                'datatype' => 'uint',
                'name' => 'ChapterUID',
                'validParents' => [
                    '54',
                ],
            ],
            '1654' => [
                'datatype' => 'string',
                'name' => 'ChapterStringUID',
                'validParents' => [
                    '54',
                ],
            ],
            '11' => [
                'datatype' => 'uint',
                'name' => 'ChapterTimeStart',
                'validParents' => [
                    '54',
                ],
            ],
            '12' => [
                'datatype' => 'uint',
                'name' => 'ChapterTimeEnd',
                'validParents' => [
                    '54',
                ],
            ],
            '18' => [
                'datatype' => 'uint',
                'name' => 'ChapterFlagHidden',
                'validParents' => [
                    '54',
                ],
            ],
            '598' => [
                'datatype' => 'uint',
                'name' => 'ChapterFlagEnabled',
                'validParents' => [
                    '54',
                ],
            ],
            '2e67' => [
                'datatype' => 'binary',
                'name' => 'ChapterSegmentUID',
                'validParents' => [
                    '54',
                ],
            ],
            '2ebc' => [
                'datatype' => 'uint',
                'name' => 'ChapterSegmentEditionUID',
                'validParents' => [
                    '54',
                ],
            ],
            '23c3' => [
                'datatype' => 'uint',
                'name' => 'ChapterPhysicalEquiv',
                'validParents' => [
                    '54',
                ],
            ],
            'f' => [
                'datatype' => 'container',
                'name' => 'ChapterTrack',
                'validParents' => [
                    '54',
                ],
            ],
            '9' => [
                'datatype' => 'uint',
                'name' => 'ChapterTrackNumber',
                'validParents' => [
                    '15',
                ],
            ],
            '0' => [
                'datatype' => 'container',
                'name' => 'ChapterDisplay',
                'validParents' => [
                    '54',
                ],
            ],
            '5' => [
                'datatype' => 'string',
                'name' => 'ChapString',
                'validParents' => [
                    '0',
                ],
            ],
            '37c' => [
                'datatype' => 'string',
                'name' => 'ChapLanguage',
                'validParents' => [
                    '0',
                ],
            ],
            '37e' => [
                'datatype' => 'string',
                'name' => 'ChapCountry',
                'validParents' => [
                    '0',
                ],
            ],
            '2944' => [
                'datatype' => 'container',
                'name' => 'ChapProcess',
                'validParents' => [
                    '54',
                ],
            ],
            '2955' => [
                'datatype' => 'uint',
                'name' => 'ChapProcessCodecID',
                'validParents' => [
                    '10564',
                ],
            ],
            '50d' => [
                'datatype' => 'binary',
                'name' => 'ChapProcessPrivate',
                'validParents' => [
                    '10564',
                ],
            ],
            '2911' => [
                'datatype' => 'container',
                'name' => 'ChapProcessCommand',
                'validParents' => [
                    '10564',
                ],
            ],
            '2922' => [
                'datatype' => 'uint',
                'name' => 'ChapProcessTime',
                'validParents' => [
                    '10513',
                ],
            ],
            '2933' => [
                'datatype' => 'binary',
                'name' => 'ChapProcessData',
                'validParents' => [
                    '10513',
                ],
            ],
            '254c367' => [
                'datatype' => 'container',
                'name' => 'Tags',
                'validParents' => [
                    '139690087',
                ],
            ],
            '3373' => [
                'datatype' => 'container',
                'name' => 'Tag',
                'validParents' => [
                    '39109479',
                ],
            ],
            '23c0' => [
                'datatype' => 'container',
                'name' => 'Targets',
                'validParents' => [
                    '13171',
                ],
            ],
            '28ca' => [
                'datatype' => 'uint',
                'name' => 'TargetTypeValue',
                'validParents' => [
                    '9152',
                ],
            ],
            '23ca' => [
                'datatype' => 'string',
                'name' => 'TargetType',
                'validParents' => [
                    '9152',
                ],
            ],
            '23c5' => [
                'datatype' => 'uint',
                'name' => 'TagTrackUID',
                'validParents' => [
                    '9152',
                ],
            ],
            '23c9' => [
                'datatype' => 'uint',
                'name' => 'TagEditionUID',
                'validParents' => [
                    '9152',
                ],
            ],
            '23c4' => [
                'datatype' => 'uint',
                'name' => 'TagChapterUID',
                'validParents' => [
                    '9152',
                ],
            ],
            '23c6' => [
                'datatype' => 'uint',
                'name' => 'TagAttachmentUID',
                'validParents' => [
                    '9152',
                ],
            ],
            '27c8' => [
                'datatype' => 'container',
                'name' => 'SimpleTag',
                'validParents' => [
                    '13171',
                    '10184',
                ],
            ],
            '5a3' => [
                'datatype' => 'string',
                'name' => 'TagName',
                'validParents' => [
                    '10184',
                ],
            ],
            '47a' => [
                'datatype' => 'string',
                'name' => 'TagLanguage',
                'validParents' => [
                    '10184',
                ],
            ],
            '484' => [
                'datatype' => 'uint',
                'name' => 'TagDefault',
                'validParents' => [
                    '10184',
                ],
            ],
            '487' => [
                'datatype' => 'string',
                'name' => 'TagString',
                'validParents' => [
                    '10184',
                ],
            ],
            '485' => [
                'datatype' => 'binary',
                'name' => 'TagBinary',
                'validParents' => [
                    '10184',
                ],
            ],
        ];
        public $ids = [
            'EBML' => 'a45dfa3',
            'EBMLVERSION' => '286',
            'EBMLREADVERSION' => '2f7',
            'EBMLMAXIDLENGTH' => '2f2',
            'EBMLMAXSIZELENGTH' => '2f3',
            'DOCTYPE' => '282',
            'DOCTYPEVERSION' => '287',
            'DOCTYPEREADVERSION' => '285',
            'VOID' => '6c',
            'CRC-32' => '3f',
            'SIGNATURESLOT' => 'b538667',
            'SIGNATUREALGO' => '3e8a',
            'SIGNATUREHASH' => '3e9a',
            'SIGNATUREPUBLICKEY' => '3ea5',
            'SIGNATURE' => '3eb5',
            'SIGNATUREELEMENTS' => '3e5b',
            'SIGNATUREELEMENTLIST' => '3e7b',
            'SIGNEDELEMENT' => '2532',
            'SEGMENT' => '8538067',
            'SEEKHEAD' => '14d9b74',
            'SEEK' => 'dbb',
            'SEEKID' => '13ab',
            'SEEKPOSITION' => '13ac',
            'INFO' => '549a966',
            'SEGMENTUID' => '33a4',
            'SEGMENTFILENAME' => '3384',
            'PREVUID' => '1cb923',
            'PREVFILENAME' => '1c83ab',
            'NEXTUID' => '1eb923',
            'NEXTFILENAME' => '1e83bb',
            'SEGMENTFAMILY' => '444',
            'CHAPTERTRANSLATE' => '2924',
            'CHAPTERTRANSLATEEDITIONUID' => '29fc',
            'CHAPTERTRANSLATECODEC' => '29bf',
            'CHAPTERTRANSLATEID' => '29a5',
            'TIMECODESCALE' => 'ad7b1',
            'DURATION' => '489',
            'DATEUTC' => '461',
            'TITLE' => '3ba9',
            'MUXINGAPP' => 'd80',
            'WRITINGAPP' => '1741',
            'CLUSTER' => 'f43b675',
            'TIMECODE' => '67',
            'SILENTTRACKS' => '1854',
            'SILENTTRACKNUMBER' => '18d7',
            'POSITION' => '27',
            'PREVSIZE' => '2b',
            'SIMPLEBLOCK' => '23',
            'BLOCKGROUP' => '20',
            'BLOCK' => '21',
            'BLOCKVIRTUAL' => '22',
            'BLOCKADDITIONS' => '35a1',
            'BLOCKMORE' => '26',
            'BLOCKADDID' => '6e',
            'BLOCKADDITIONAL' => '25',
            'BLOCKDURATION' => '1b',
            'REFERENCEPRIORITY' => '7a',
            'REFERENCEBLOCK' => '7b',
            'REFERENCEVIRTUAL' => '7d',
            'CODECSTATE' => '24',
            'DISCARDPADDING' => '35a2',
            'SLICES' => 'e',
            'TIMESLICE' => '68',
            'LACENUMBER' => '4c',
            'FRAMENUMBER' => '4d',
            'BLOCKADDITIONID' => '4b',
            'DELAY' => '4e',
            'SLICEDURATION' => '4f',
            'REFERENCEFRAME' => '48',
            'REFERENCEOFFSET' => '49',
            'REFERENCETIMECODE' => '4a',
            'ENCRYPTEDBLOCK' => '2f',
            'TRACKS' => '654ae6b',
            'TRACKENTRY' => '2e',
            'TRACKNUMBER' => '57',
            'TRACKUID' => '33c5',
            'TRACKTYPE' => '3',
            'FLAGENABLED' => '39',
            'FLAGDEFAULT' => '8',
            'FLAGFORCED' => '15aa',
            'FLAGLACING' => '1c',
            'MINCACHE' => '2de7',
            'MAXCACHE' => '2df8',
            'DEFAULTDURATION' => '3e383',
            'DEFAULTDECODEDFIELDDURATION' => '34e7a',
            'TRACKTIMECODESCALE' => '3314f',
            'TRACKOFFSET' => '137f',
            'MAXBLOCKADDITIONID' => '15ee',
            'NAME' => '136e',
            'LANGUAGE' => '2b59c',
            'CODECID' => '6',
            'CODECPRIVATE' => '23a2',
            'CODECNAME' => '58688',
            'ATTACHMENTLINK' => '3446',
            'CODECSETTINGS' => '1a9697',
            'CODECINFOURL' => '1b4040',
            'CODECDOWNLOADURL' => '6b240',
            'CODECDECODEALL' => '2a',
            'TRACKOVERLAY' => '2fab',
            'CODECDELAY' => '16aa',
            'SEEKPREROLL' => '16bb',
            'TRACKTRANSLATE' => '2624',
            'TRACKTRANSLATEEDITIONUID' => '26fc',
            'TRACKTRANSLATECODEC' => '26bf',
            'TRACKTRANSLATETRACKID' => '26a5',
            'VIDEO' => '60',
            'FLAGINTERLACED' => '1a',
            'STEREOMODE' => '13b8',
            'ALPHAMODE' => '13c0',
            'OLDSTEREOMODE' => '13b9',
            'PIXELWIDTH' => '30',
            'PIXELHEIGHT' => '3a',
            'PIXELCROPBOTTOM' => '14aa',
            'PIXELCROPTOP' => '14bb',
            'PIXELCROPLEFT' => '14cc',
            'PIXELCROPRIGHT' => '14dd',
            'DISPLAYWIDTH' => '14b0',
            'DISPLAYHEIGHT' => '14ba',
            'DISPLAYUNIT' => '14b2',
            'ASPECTRATIOTYPE' => '14b3',
            'COLOURSPACE' => 'eb524',
            'GAMMAVALUE' => 'fb523',
            'FRAMERATE' => '383e3',
            'AUDIO' => '61',
            'SAMPLINGFREQUENCY' => '35',
            'OUTPUTSAMPLINGFREQUENCY' => '38b5',
            'CHANNELS' => '1f',
            'CHANNELPOSITIONS' => '3d7b',
            'BITDEPTH' => '2264',
            'TRACKOPERATION' => '62',
            'TRACKCOMBINEPLANES' => '63',
            'TRACKPLANE' => '64',
            'TRACKPLANEUID' => '65',
            'TRACKPLANETYPE' => '66',
            'TRACKJOINBLOCKS' => '69',
            'TRACKJOINUID' => '6d',
            'TRICKTRACKUID' => '40',
            'TRICKTRACKSEGMENTUID' => '41',
            'TRICKTRACKFLAG' => '46',
            'TRICKMASTERTRACKUID' => '47',
            'TRICKMASTERTRACKSEGMENTUID' => '44',
            'CONTENTENCODINGS' => '2d80',
            'CONTENTENCODING' => '2240',
            'CONTENTENCODINGORDER' => '1031',
            'CONTENTENCODINGSCOPE' => '1032',
            'CONTENTENCODINGTYPE' => '1033',
            'CONTENTCOMPRESSION' => '1034',
            'CONTENTCOMPALGO' => '254',
            'CONTENTCOMPSETTINGS' => '255',
            'CONTENTENCRYPTION' => '1035',
            'CONTENTENCALGO' => '7e1',
            'CONTENTENCKEYID' => '7e2',
            'CONTENTSIGNATURE' => '7e3',
            'CONTENTSIGKEYID' => '7e4',
            'CONTENTSIGALGO' => '7e5',
            'CONTENTSIGHASHALGO' => '7e6',
            'CUES' => 'c53bb6b',
            'CUEPOINT' => '3b',
            'CUETIME' => '33',
            'CUETRACKPOSITIONS' => '37',
            'CUETRACK' => '77',
            'CUECLUSTERPOSITION' => '71',
            'CUERELATIVEPOSITION' => '70',
            'CUEDURATION' => '32',
            'CUEBLOCKNUMBER' => '1378',
            'CUECODECSTATE' => '6a',
            'CUEREFERENCE' => '5b',
            'CUEREFTIME' => '16',
            'CUEREFCLUSTER' => '17',
            'CUEREFNUMBER' => '135f',
            'CUEREFCODECSTATE' => '6b',
            'ATTACHMENTS' => '941a469',
            'ATTACHEDFILE' => '21a7',
            'FILEDESCRIPTION' => '67e',
            'FILENAME' => '66e',
            'FILEMIMETYPE' => '660',
            'FILEDATA' => '65c',
            'FILEUID' => '6ae',
            'FILEREFERRAL' => '675',
            'FILEUSEDSTARTTIME' => '661',
            'FILEUSEDENDTIME' => '662',
            'CHAPTERS' => '43a770',
            'EDITIONENTRY' => '5b9',
            'EDITIONUID' => '5bc',
            'EDITIONFLAGHIDDEN' => '5bd',
            'EDITIONFLAGDEFAULT' => '5db',
            'EDITIONFLAGORDERED' => '5dd',
            'CHAPTERATOM' => '36',
            'CHAPTERUID' => '33c4',
            'CHAPTERSTRINGUID' => '1654',
            'CHAPTERTIMESTART' => '11',
            'CHAPTERTIMEEND' => '12',
            'CHAPTERFLAGHIDDEN' => '18',
            'CHAPTERFLAGENABLED' => '598',
            'CHAPTERSEGMENTUID' => '2e67',
            'CHAPTERSEGMENTEDITIONUID' => '2ebc',
            'CHAPTERPHYSICALEQUIV' => '23c3',
            'CHAPTERTRACK' => 'f',
            'CHAPTERTRACKNUMBER' => '9',
            'CHAPTERDISPLAY' => '0',
            'CHAPSTRING' => '5',
            'CHAPLANGUAGE' => '37c',
            'CHAPCOUNTRY' => '37e',
            'CHAPPROCESS' => '2944',
            'CHAPPROCESSCODECID' => '2955',
            'CHAPPROCESSPRIVATE' => '50d',
            'CHAPPROCESSCOMMAND' => '2911',
            'CHAPPROCESSTIME' => '2922',
            'CHAPPROCESSDATA' => '2933',
            'TAGS' => '254c367',
            'TAG' => '3373',
            'TARGETS' => '23c0',
            'TARGETTYPEVALUE' => '28ca',
            'TARGETTYPE' => '23ca',
            'TAGTRACKUID' => '23c5',
            'TAGEDITIONUID' => '23c9',
            'TAGCHAPTERUID' => '23c4',
            'TAGATTACHMENTUID' => '23c6',
            'SIMPLETAG' => '27c8',
            'TAGNAME' => '5a3',
            'TAGLANGUAGE' => '47a',
            'TAGDEFAULT' => '484',
            'TAGSTRING' => '487',
            'TAGBINARY' => '485',
        ];
        public static $instance;

        public static function singleton()
        {
                self::$instance || self::$instance = new fileHandle();
                return self::$instance;
        }

        public static function exists($id)
        {
                return isset(self::singleton()->elements[$id]);
        }

        public static function name($id)
        {
                if (!isset(self::singleton()->elements[$id]))
                {
                        return null;
                }
                return self::singleton()->elements[$id]['name'];
        }

        public static function id($name)
        {
                $name = strtoupper($name);
                if (!isset(self::singleton()->ids[$name]))
                {
                        return null;
                }
                return self::singleton()->ids[$name];
        }

        public static function datatype($id)
        {
                if ($id == 'root')
                {
                        return 'container';
                }
                elseif (!isset(self::singleton()->elements[$id]))
                {
                        return 'binary';
                }
                return self::singleton()->elements[$id]['datatype'];
        }

        public static function validChild($id1, $id2)
        {
                if (!isset(self::singleton()->elements[$id2]))
                {
                        return true;
                }
                return in_array('*', self::singleton()->elements[$id2]['validParents']) || in_array($id1, self::singleton()->elements[$id2]['validParents']);
        }

}