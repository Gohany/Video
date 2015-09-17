<?php

// Information needed to parse all possible element types in a document
class EBMLElements
{

        public $elements = [
            172351395 => [
                'datatype' => 'container',
                'name' => 'EBML',
                'validParents' => [
                    'root',
                ],
            ],
            646 => [
                'datatype' => 'uint',
                'name' => 'EBMLVersion',
                'validParents' => [
                    172351395,
                ],
            ],
            759 => [
                'datatype' => 'uint',
                'name' => 'EBMLReadVersion',
                'validParents' => [
                    172351395,
                ],
            ],
            754 => [
                'datatype' => 'uint',
                'name' => 'EBMLMaxIDLength',
                'validParents' => [
                    172351395,
                ],
            ],
            755 => [
                'datatype' => 'uint',
                'name' => 'EBMLMaxSizeLength',
                'validParents' => [
                    172351395,
                ],
            ],
            642 => [
                'datatype' => 'string',
                'name' => 'DocType',
                'validParents' => [
                    172351395,
                ],
            ],
            647 => [
                'datatype' => 'uint',
                'name' => 'DocTypeVersion',
                'validParents' => [
                    172351395,
                ],
            ],
            645 => [
                'datatype' => 'uint',
                'name' => 'DocTypeReadVersion',
                'validParents' => [
                    172351395,
                ],
            ],
            108 => [
                'datatype' => 'binary',
                'name' => 'Void',
                'validParents' => [
                    '*',
                ],
            ],
            63 => [
                'datatype' => 'binary',
                'name' => 'CRC-32',
                'validParents' => [
                    '*',
                ],
            ],
            190023271 => [
                'datatype' => 'container',
                'name' => 'SignatureSlot',
                'validParents' => [
                    '*',
                ],
            ],
            16010 => [
                'datatype' => 'uint',
                'name' => 'SignatureAlgo',
                'validParents' => [
                    190023271,
                ],
            ],
            16026 => [
                'datatype' => 'uint',
                'name' => 'SignatureHash',
                'validParents' => [
                    190023271,
                ],
            ],
            16037 => [
                'datatype' => 'binary',
                'name' => 'SignaturePublicKey',
                'validParents' => [
                    190023271,
                ],
            ],
            16053 => [
                'datatype' => 'binary',
                'name' => 'Signature',
                'validParents' => [
                    190023271,
                ],
            ],
            15963 => [
                'datatype' => 'container',
                'name' => 'SignatureElements',
                'validParents' => [
                    190023271,
                ],
            ],
            15995 => [
                'datatype' => 'container',
                'name' => 'SignatureElementList',
                'validParents' => [
                    15963,
                ],
            ],
            9522 => [
                'datatype' => 'binary',
                'name' => 'SignedElement',
                'validParents' => [
                    15995,
                ],
            ],
            139690087 => [
                'datatype' => 'container',
                'name' => 'Segment',
                'validParents' => [
                    'root',
                ],
            ],
            21863284 => [
                'datatype' => 'container',
                'name' => 'SeekHead',
                'validParents' => [
                    139690087,
                ],
            ],
            3515 => [
                'datatype' => 'container',
                'name' => 'Seek',
                'validParents' => [
                    21863284,
                ],
            ],
            5035 => [
                'datatype' => 'binary',
                'name' => 'SeekID',
                'validParents' => [
                    3515,
                ],
            ],
            5036 => [
                'datatype' => 'uint',
                'name' => 'SeekPosition',
                'validParents' => [
                    3515,
                ],
            ],
            88713574 => [
                'datatype' => 'container',
                'name' => 'Info',
                'validParents' => [
                    139690087,
                ],
            ],
            13220 => [
                'datatype' => 'binary',
                'name' => 'SegmentUID',
                'validParents' => [
                    88713574,
                ],
            ],
            13188 => [
                'datatype' => 'string',
                'name' => 'SegmentFilename',
                'validParents' => [
                    88713574,
                ],
            ],
            1882403 => [
                'datatype' => 'binary',
                'name' => 'PrevUID',
                'validParents' => [
                    88713574,
                ],
            ],
            1868715 => [
                'datatype' => 'string',
                'name' => 'PrevFilename',
                'validParents' => [
                    88713574,
                ],
            ],
            2013475 => [
                'datatype' => 'binary',
                'name' => 'NextUID',
                'validParents' => [
                    88713574,
                ],
            ],
            1999803 => [
                'datatype' => 'string',
                'name' => 'NextFilename',
                'validParents' => [
                    88713574,
                ],
            ],
            1092 => [
                'datatype' => 'binary',
                'name' => 'SegmentFamily',
                'validParents' => [
                    88713574,
                ],
            ],
            10532 => [
                'datatype' => 'container',
                'name' => 'ChapterTranslate',
                'validParents' => [
                    88713574,
                ],
            ],
            10748 => [
                'datatype' => 'uint',
                'name' => 'ChapterTranslateEditionUID',
                'validParents' => [
                    10532,
                ],
            ],
            10687 => [
                'datatype' => 'uint',
                'name' => 'ChapterTranslateCodec',
                'validParents' => [
                    10532,
                ],
            ],
            10661 => [
                'datatype' => 'binary',
                'name' => 'ChapterTranslateID',
                'validParents' => [
                    10532,
                ],
            ],
            710577 => [
                'datatype' => 'uint',
                'name' => 'TimecodeScale',
                'validParents' => [
                    88713574,
                ],
            ],
            1161 => [
                'datatype' => 'float',
                'name' => 'Duration',
                'validParents' => [
                    88713574,
                ],
            ],
            1121 => [
                'datatype' => 'date',
                'name' => 'DateUTC',
                'validParents' => [
                    88713574,
                ],
            ],
            15273 => [
                'datatype' => 'string',
                'name' => 'Title',
                'validParents' => [
                    88713574,
                ],
            ],
            3456 => [
                'datatype' => 'string',
                'name' => 'MuxingApp',
                'validParents' => [
                    88713574,
                ],
            ],
            5953 => [
                'datatype' => 'string',
                'name' => 'WritingApp',
                'validParents' => [
                    88713574,
                ],
            ],
            256095861 => [
                'datatype' => 'container',
                'name' => 'Cluster',
                'validParents' => [
                    139690087,
                ],
            ],
            103 => [
                'datatype' => 'uint',
                'name' => 'Timecode',
                'validParents' => [
                    256095861,
                ],
            ],
            6228 => [
                'datatype' => 'container',
                'name' => 'SilentTracks',
                'validParents' => [
                    256095861,
                ],
            ],
            6359 => [
                'datatype' => 'uint',
                'name' => 'SilentTrackNumber',
                'validParents' => [
                    6228,
                ],
            ],
            39 => [
                'datatype' => 'uint',
                'name' => 'Position',
                'validParents' => [
                    256095861,
                ],
            ],
            43 => [
                'datatype' => 'uint',
                'name' => 'PrevSize',
                'validParents' => [
                    256095861,
                ],
            ],
            35 => [
                'datatype' => 'binary',
                'name' => 'SimpleBlock',
                'validParents' => [
                    256095861,
                ],
            ],
            32 => [
                'datatype' => 'container',
                'name' => 'BlockGroup',
                'validParents' => [
                    256095861,
                ],
            ],
            33 => [
                'datatype' => 'binary',
                'name' => 'Block',
                'validParents' => [
                    32,
                ],
            ],
            34 => [
                'datatype' => 'binary',
                'name' => 'BlockVirtual',
                'validParents' => [
                    32,
                ],
            ],
            13729 => [
                'datatype' => 'container',
                'name' => 'BlockAdditions',
                'validParents' => [
                    32,
                ],
            ],
            38 => [
                'datatype' => 'container',
                'name' => 'BlockMore',
                'validParents' => [
                    13729,
                ],
            ],
            110 => [
                'datatype' => 'uint',
                'name' => 'BlockAddID',
                'validParents' => [
                    38,
                ],
            ],
            37 => [
                'datatype' => 'binary',
                'name' => 'BlockAdditional',
                'validParents' => [
                    38,
                ],
            ],
            27 => [
                'datatype' => 'uint',
                'name' => 'BlockDuration',
                'validParents' => [
                    32,
                ],
            ],
            122 => [
                'datatype' => 'uint',
                'name' => 'ReferencePriority',
                'validParents' => [
                    32,
                ],
            ],
            123 => [
                'datatype' => 'int',
                'name' => 'ReferenceBlock',
                'validParents' => [
                    32,
                ],
            ],
            125 => [
                'datatype' => 'int',
                'name' => 'ReferenceVirtual',
                'validParents' => [
                    32,
                ],
            ],
            36 => [
                'datatype' => 'binary',
                'name' => 'CodecState',
                'validParents' => [
                    32,
                ],
            ],
            13730 => [
                'datatype' => 'int',
                'name' => 'DiscardPadding',
                'validParents' => [
                    32,
                ],
            ],
            14 => [
                'datatype' => 'container',
                'name' => 'Slices',
                'validParents' => [
                    32,
                ],
            ],
            104 => [
                'datatype' => 'container',
                'name' => 'TimeSlice',
                'validParents' => [
                    14,
                ],
            ],
            76 => [
                'datatype' => 'uint',
                'name' => 'LaceNumber',
                'validParents' => [
                    104,
                ],
            ],
            77 => [
                'datatype' => 'uint',
                'name' => 'FrameNumber',
                'validParents' => [
                    104,
                ],
            ],
            75 => [
                'datatype' => 'uint',
                'name' => 'BlockAdditionID',
                'validParents' => [
                    104,
                ],
            ],
            78 => [
                'datatype' => 'uint',
                'name' => 'Delay',
                'validParents' => [
                    104,
                ],
            ],
            79 => [
                'datatype' => 'uint',
                'name' => 'SliceDuration',
                'validParents' => [
                    104,
                ],
            ],
            72 => [
                'datatype' => 'container',
                'name' => 'ReferenceFrame',
                'validParents' => [
                    32,
                ],
            ],
            73 => [
                'datatype' => 'uint',
                'name' => 'ReferenceOffset',
                'validParents' => [
                    72,
                ],
            ],
            74 => [
                'datatype' => 'uint',
                'name' => 'ReferenceTimeCode',
                'validParents' => [
                    72,
                ],
            ],
            47 => [
                'datatype' => 'binary',
                'name' => 'EncryptedBlock',
                'validParents' => [
                    256095861,
                ],
            ],
            106212971 => [
                'datatype' => 'container',
                'name' => 'Tracks',
                'validParents' => [
                    139690087,
                ],
            ],
            46 => [
                'datatype' => 'container',
                'name' => 'TrackEntry',
                'validParents' => [
                    106212971,
                ],
            ],
            87 => [
                'datatype' => 'uint',
                'name' => 'TrackNumber',
                'validParents' => [
                    46,
                ],
            ],
            13253 => [
                'datatype' => 'uint',
                'name' => 'TrackUID',
                'validParents' => [
                    46,
                ],
            ],
            3 => [
                'datatype' => 'uint',
                'name' => 'TrackType',
                'validParents' => [
                    46,
                ],
            ],
            57 => [
                'datatype' => 'uint',
                'name' => 'FlagEnabled',
                'validParents' => [
                    46,
                ],
            ],
            8 => [
                'datatype' => 'uint',
                'name' => 'FlagDefault',
                'validParents' => [
                    46,
                ],
            ],
            5546 => [
                'datatype' => 'uint',
                'name' => 'FlagForced',
                'validParents' => [
                    46,
                ],
            ],
            28 => [
                'datatype' => 'uint',
                'name' => 'FlagLacing',
                'validParents' => [
                    46,
                ],
            ],
            11751 => [
                'datatype' => 'uint',
                'name' => 'MinCache',
                'validParents' => [
                    46,
                ],
            ],
            11768 => [
                'datatype' => 'uint',
                'name' => 'MaxCache',
                'validParents' => [
                    46,
                ],
            ],
            254851 => [
                'datatype' => 'uint',
                'name' => 'DefaultDuration',
                'validParents' => [
                    46,
                ],
            ],
            216698 => [
                'datatype' => 'uint',
                'name' => 'DefaultDecodedFieldDuration',
                'validParents' => [
                    46,
                ],
            ],
            209231 => [
                'datatype' => 'float',
                'name' => 'TrackTimecodeScale',
                'validParents' => [
                    46,
                ],
            ],
            4991 => [
                'datatype' => 'int',
                'name' => 'TrackOffset',
                'validParents' => [
                    46,
                ],
            ],
            5614 => [
                'datatype' => 'uint',
                'name' => 'MaxBlockAdditionID',
                'validParents' => [
                    46,
                ],
            ],
            4974 => [
                'datatype' => 'string',
                'name' => 'Name',
                'validParents' => [
                    46,
                ],
            ],
            177564 => [
                'datatype' => 'string',
                'name' => 'Language',
                'validParents' => [
                    46,
                ],
            ],
            6 => [
                'datatype' => 'string',
                'name' => 'CodecID',
                'validParents' => [
                    46,
                ],
            ],
            9122 => [
                'datatype' => 'binary',
                'name' => 'CodecPrivate',
                'validParents' => [
                    46,
                ],
            ],
            362120 => [
                'datatype' => 'string',
                'name' => 'CodecName',
                'validParents' => [
                    46,
                ],
            ],
            13382 => [
                'datatype' => 'uint',
                'name' => 'AttachmentLink',
                'validParents' => [
                    46,
                ],
            ],
            1742487 => [
                'datatype' => 'string',
                'name' => 'CodecSettings',
                'validParents' => [
                    46,
                ],
            ],
            1785920 => [
                'datatype' => 'string',
                'name' => 'CodecInfoURL',
                'validParents' => [
                    46,
                ],
            ],
            438848 => [
                'datatype' => 'string',
                'name' => 'CodecDownloadURL',
                'validParents' => [
                    46,
                ],
            ],
            42 => [
                'datatype' => 'uint',
                'name' => 'CodecDecodeAll',
                'validParents' => [
                    46,
                ],
            ],
            12203 => [
                'datatype' => 'uint',
                'name' => 'TrackOverlay',
                'validParents' => [
                    46,
                ],
            ],
            5802 => [
                'datatype' => 'uint',
                'name' => 'CodecDelay',
                'validParents' => [
                    46,
                ],
            ],
            5819 => [
                'datatype' => 'uint',
                'name' => 'SeekPreRoll',
                'validParents' => [
                    46,
                ],
            ],
            9764 => [
                'datatype' => 'container',
                'name' => 'TrackTranslate',
                'validParents' => [
                    46,
                ],
            ],
            9980 => [
                'datatype' => 'uint',
                'name' => 'TrackTranslateEditionUID',
                'validParents' => [
                    9764,
                ],
            ],
            9919 => [
                'datatype' => 'uint',
                'name' => 'TrackTranslateCodec',
                'validParents' => [
                    9764,
                ],
            ],
            9893 => [
                'datatype' => 'binary',
                'name' => 'TrackTranslateTrackID',
                'validParents' => [
                    9764,
                ],
            ],
            96 => [
                'datatype' => 'container',
                'name' => 'Video',
                'validParents' => [
                    46,
                ],
            ],
            26 => [
                'datatype' => 'uint',
                'name' => 'FlagInterlaced',
                'validParents' => [
                    96,
                ],
            ],
            5048 => [
                'datatype' => 'uint',
                'name' => 'StereoMode',
                'validParents' => [
                    96,
                ],
            ],
            5056 => [
                'datatype' => 'uint',
                'name' => 'AlphaMode',
                'validParents' => [
                    96,
                ],
            ],
            5049 => [
                'datatype' => 'uint',
                'name' => 'OldStereoMode',
                'validParents' => [
                    96,
                ],
            ],
            48 => [
                'datatype' => 'uint',
                'name' => 'PixelWidth',
                'validParents' => [
                    96,
                ],
            ],
            58 => [
                'datatype' => 'uint',
                'name' => 'PixelHeight',
                'validParents' => [
                    96,
                ],
            ],
            5290 => [
                'datatype' => 'uint',
                'name' => 'PixelCropBottom',
                'validParents' => [
                    96,
                ],
            ],
            5307 => [
                'datatype' => 'uint',
                'name' => 'PixelCropTop',
                'validParents' => [
                    96,
                ],
            ],
            5324 => [
                'datatype' => 'uint',
                'name' => 'PixelCropLeft',
                'validParents' => [
                    96,
                ],
            ],
            5341 => [
                'datatype' => 'uint',
                'name' => 'PixelCropRight',
                'validParents' => [
                    96,
                ],
            ],
            5296 => [
                'datatype' => 'uint',
                'name' => 'DisplayWidth',
                'validParents' => [
                    96,
                ],
            ],
            5306 => [
                'datatype' => 'uint',
                'name' => 'DisplayHeight',
                'validParents' => [
                    96,
                ],
            ],
            5298 => [
                'datatype' => 'uint',
                'name' => 'DisplayUnit',
                'validParents' => [
                    96,
                ],
            ],
            5299 => [
                'datatype' => 'uint',
                'name' => 'AspectRatioType',
                'validParents' => [
                    96,
                ],
            ],
            963876 => [
                'datatype' => 'binary',
                'name' => 'ColourSpace',
                'validParents' => [
                    96,
                ],
            ],
            1029411 => [
                'datatype' => 'float',
                'name' => 'GammaValue',
                'validParents' => [
                    96,
                ],
            ],
            230371 => [
                'datatype' => 'float',
                'name' => 'FrameRate',
                'validParents' => [
                    96,
                ],
            ],
            97 => [
                'datatype' => 'container',
                'name' => 'Audio',
                'validParents' => [
                    46,
                ],
            ],
            53 => [
                'datatype' => 'float',
                'name' => 'SamplingFrequency',
                'validParents' => [
                    97,
                ],
            ],
            14517 => [
                'datatype' => 'float',
                'name' => 'OutputSamplingFrequency',
                'validParents' => [
                    97,
                ],
            ],
            31 => [
                'datatype' => 'uint',
                'name' => 'Channels',
                'validParents' => [
                    97,
                ],
            ],
            15739 => [
                'datatype' => 'binary',
                'name' => 'ChannelPositions',
                'validParents' => [
                    97,
                ],
            ],
            8804 => [
                'datatype' => 'uint',
                'name' => 'BitDepth',
                'validParents' => [
                    97,
                ],
            ],
            98 => [
                'datatype' => 'container',
                'name' => 'TrackOperation',
                'validParents' => [
                    46,
                ],
            ],
            99 => [
                'datatype' => 'container',
                'name' => 'TrackCombinePlanes',
                'validParents' => [
                    98,
                ],
            ],
            100 => [
                'datatype' => 'container',
                'name' => 'TrackPlane',
                'validParents' => [
                    99,
                ],
            ],
            101 => [
                'datatype' => 'uint',
                'name' => 'TrackPlaneUID',
                'validParents' => [
                    100,
                ],
            ],
            102 => [
                'datatype' => 'uint',
                'name' => 'TrackPlaneType',
                'validParents' => [
                    100,
                ],
            ],
            105 => [
                'datatype' => 'container',
                'name' => 'TrackJoinBlocks',
                'validParents' => [
                    98,
                ],
            ],
            109 => [
                'datatype' => 'uint',
                'name' => 'TrackJoinUID',
                'validParents' => [
                    105,
                ],
            ],
            64 => [
                'datatype' => 'uint',
                'name' => 'TrickTrackUID',
                'validParents' => [
                    46,
                ],
            ],
            65 => [
                'datatype' => 'binary',
                'name' => 'TrickTrackSegmentUID',
                'validParents' => [
                    46,
                ],
            ],
            70 => [
                'datatype' => 'uint',
                'name' => 'TrickTrackFlag',
                'validParents' => [
                    46,
                ],
            ],
            71 => [
                'datatype' => 'uint',
                'name' => 'TrickMasterTrackUID',
                'validParents' => [
                    46,
                ],
            ],
            68 => [
                'datatype' => 'binary',
                'name' => 'TrickMasterTrackSegmentUID',
                'validParents' => [
                    46,
                ],
            ],
            11648 => [
                'datatype' => 'container',
                'name' => 'ContentEncodings',
                'validParents' => [
                    46,
                ],
            ],
            8768 => [
                'datatype' => 'container',
                'name' => 'ContentEncoding',
                'validParents' => [
                    11648,
                ],
            ],
            4145 => [
                'datatype' => 'uint',
                'name' => 'ContentEncodingOrder',
                'validParents' => [
                    8768,
                ],
            ],
            4146 => [
                'datatype' => 'uint',
                'name' => 'ContentEncodingScope',
                'validParents' => [
                    8768,
                ],
            ],
            4147 => [
                'datatype' => 'uint',
                'name' => 'ContentEncodingType',
                'validParents' => [
                    8768,
                ],
            ],
            4148 => [
                'datatype' => 'container',
                'name' => 'ContentCompression',
                'validParents' => [
                    8768,
                ],
            ],
            596 => [
                'datatype' => 'uint',
                'name' => 'ContentCompAlgo',
                'validParents' => [
                    4148,
                ],
            ],
            597 => [
                'datatype' => 'binary',
                'name' => 'ContentCompSettings',
                'validParents' => [
                    4148,
                ],
            ],
            4149 => [
                'datatype' => 'container',
                'name' => 'ContentEncryption',
                'validParents' => [
                    8768,
                ],
            ],
            2017 => [
                'datatype' => 'uint',
                'name' => 'ContentEncAlgo',
                'validParents' => [
                    4149,
                ],
            ],
            2018 => [
                'datatype' => 'binary',
                'name' => 'ContentEncKeyID',
                'validParents' => [
                    4149,
                ],
            ],
            2019 => [
                'datatype' => 'binary',
                'name' => 'ContentSignature',
                'validParents' => [
                    4149,
                ],
            ],
            2020 => [
                'datatype' => 'binary',
                'name' => 'ContentSigKeyID',
                'validParents' => [
                    4149,
                ],
            ],
            2021 => [
                'datatype' => 'uint',
                'name' => 'ContentSigAlgo',
                'validParents' => [
                    4149,
                ],
            ],
            2022 => [
                'datatype' => 'uint',
                'name' => 'ContentSigHashAlgo',
                'validParents' => [
                    4149,
                ],
            ],
            206814059 => [
                'datatype' => 'container',
                'name' => 'Cues',
                'validParents' => [
                    139690087,
                ],
            ],
            59 => [
                'datatype' => 'container',
                'name' => 'CuePoint',
                'validParents' => [
                    206814059,
                ],
            ],
            51 => [
                'datatype' => 'uint',
                'name' => 'CueTime',
                'validParents' => [
                    59,
                ],
            ],
            55 => [
                'datatype' => 'container',
                'name' => 'CueTrackPositions',
                'validParents' => [
                    59,
                ],
            ],
            119 => [
                'datatype' => 'uint',
                'name' => 'CueTrack',
                'validParents' => [
                    55,
                ],
            ],
            113 => [
                'datatype' => 'uint',
                'name' => 'CueClusterPosition',
                'validParents' => [
                    55,
                ],
            ],
            112 => [
                'datatype' => 'uint',
                'name' => 'CueRelativePosition',
                'validParents' => [
                    55,
                ],
            ],
            50 => [
                'datatype' => 'uint',
                'name' => 'CueDuration',
                'validParents' => [
                    55,
                ],
            ],
            4984 => [
                'datatype' => 'uint',
                'name' => 'CueBlockNumber',
                'validParents' => [
                    55,
                ],
            ],
            106 => [
                'datatype' => 'uint',
                'name' => 'CueCodecState',
                'validParents' => [
                    55,
                ],
            ],
            91 => [
                'datatype' => 'container',
                'name' => 'CueReference',
                'validParents' => [
                    55,
                ],
            ],
            22 => [
                'datatype' => 'uint',
                'name' => 'CueRefTime',
                'validParents' => [
                    91,
                ],
            ],
            23 => [
                'datatype' => 'uint',
                'name' => 'CueRefCluster',
                'validParents' => [
                    91,
                ],
            ],
            4959 => [
                'datatype' => 'uint',
                'name' => 'CueRefNumber',
                'validParents' => [
                    91,
                ],
            ],
            107 => [
                'datatype' => 'uint',
                'name' => 'CueRefCodecState',
                'validParents' => [
                    91,
                ],
            ],
            155296873 => [
                'datatype' => 'container',
                'name' => 'Attachments',
                'validParents' => [
                    139690087,
                ],
            ],
            8615 => [
                'datatype' => 'container',
                'name' => 'AttachedFile',
                'validParents' => [
                    155296873,
                ],
            ],
            1662 => [
                'datatype' => 'string',
                'name' => 'FileDescription',
                'validParents' => [
                    8615,
                ],
            ],
            1646 => [
                'datatype' => 'string',
                'name' => 'FileName',
                'validParents' => [
                    8615,
                ],
            ],
            1632 => [
                'datatype' => 'string',
                'name' => 'FileMimeType',
                'validParents' => [
                    8615,
                ],
            ],
            1628 => [
                'datatype' => 'binary',
                'name' => 'FileData',
                'validParents' => [
                    8615,
                ],
            ],
            1710 => [
                'datatype' => 'uint',
                'name' => 'FileUID',
                'validParents' => [
                    8615,
                ],
            ],
            1653 => [
                'datatype' => 'binary',
                'name' => 'FileReferral',
                'validParents' => [
                    8615,
                ],
            ],
            1633 => [
                'datatype' => 'uint',
                'name' => 'FileUsedStartTime',
                'validParents' => [
                    8615,
                ],
            ],
            1634 => [
                'datatype' => 'uint',
                'name' => 'FileUsedEndTime',
                'validParents' => [
                    8615,
                ],
            ],
            4433776 => [
                'datatype' => 'container',
                'name' => 'Chapters',
                'validParents' => [
                    139690087,
                ],
            ],
            1465 => [
                'datatype' => 'container',
                'name' => 'EditionEntry',
                'validParents' => [
                    4433776,
                ],
            ],
            1468 => [
                'datatype' => 'uint',
                'name' => 'EditionUID',
                'validParents' => [
                    1465,
                ],
            ],
            1469 => [
                'datatype' => 'uint',
                'name' => 'EditionFlagHidden',
                'validParents' => [
                    1465,
                ],
            ],
            1499 => [
                'datatype' => 'uint',
                'name' => 'EditionFlagDefault',
                'validParents' => [
                    1465,
                ],
            ],
            1501 => [
                'datatype' => 'uint',
                'name' => 'EditionFlagOrdered',
                'validParents' => [
                    1465,
                ],
            ],
            54 => [
                'datatype' => 'container',
                'name' => 'ChapterAtom',
                'validParents' => [
                    1465,
                    54,
                ],
            ],
            13252 => [
                'datatype' => 'uint',
                'name' => 'ChapterUID',
                'validParents' => [
                    54,
                ],
            ],
            5716 => [
                'datatype' => 'string',
                'name' => 'ChapterStringUID',
                'validParents' => [
                    54,
                ],
            ],
            17 => [
                'datatype' => 'uint',
                'name' => 'ChapterTimeStart',
                'validParents' => [
                    54,
                ],
            ],
            18 => [
                'datatype' => 'uint',
                'name' => 'ChapterTimeEnd',
                'validParents' => [
                    54,
                ],
            ],
            24 => [
                'datatype' => 'uint',
                'name' => 'ChapterFlagHidden',
                'validParents' => [
                    54,
                ],
            ],
            1432 => [
                'datatype' => 'uint',
                'name' => 'ChapterFlagEnabled',
                'validParents' => [
                    54,
                ],
            ],
            11879 => [
                'datatype' => 'binary',
                'name' => 'ChapterSegmentUID',
                'validParents' => [
                    54,
                ],
            ],
            11964 => [
                'datatype' => 'uint',
                'name' => 'ChapterSegmentEditionUID',
                'validParents' => [
                    54,
                ],
            ],
            9155 => [
                'datatype' => 'uint',
                'name' => 'ChapterPhysicalEquiv',
                'validParents' => [
                    54,
                ],
            ],
            15 => [
                'datatype' => 'container',
                'name' => 'ChapterTrack',
                'validParents' => [
                    54,
                ],
            ],
            9 => [
                'datatype' => 'uint',
                'name' => 'ChapterTrackNumber',
                'validParents' => [
                    15,
                ],
            ],
            0 => [
                'datatype' => 'container',
                'name' => 'ChapterDisplay',
                'validParents' => [
                    54,
                ],
            ],
            5 => [
                'datatype' => 'string',
                'name' => 'ChapString',
                'validParents' => [
                    0,
                ],
            ],
            892 => [
                'datatype' => 'string',
                'name' => 'ChapLanguage',
                'validParents' => [
                    0,
                ],
            ],
            894 => [
                'datatype' => 'string',
                'name' => 'ChapCountry',
                'validParents' => [
                    0,
                ],
            ],
            10564 => [
                'datatype' => 'container',
                'name' => 'ChapProcess',
                'validParents' => [
                    54,
                ],
            ],
            10581 => [
                'datatype' => 'uint',
                'name' => 'ChapProcessCodecID',
                'validParents' => [
                    10564,
                ],
            ],
            1293 => [
                'datatype' => 'binary',
                'name' => 'ChapProcessPrivate',
                'validParents' => [
                    10564,
                ],
            ],
            10513 => [
                'datatype' => 'container',
                'name' => 'ChapProcessCommand',
                'validParents' => [
                    10564,
                ],
            ],
            10530 => [
                'datatype' => 'uint',
                'name' => 'ChapProcessTime',
                'validParents' => [
                    10513,
                ],
            ],
            10547 => [
                'datatype' => 'binary',
                'name' => 'ChapProcessData',
                'validParents' => [
                    10513,
                ],
            ],
            39109479 => [
                'datatype' => 'container',
                'name' => 'Tags',
                'validParents' => [
                    139690087,
                ],
            ],
            13171 => [
                'datatype' => 'container',
                'name' => 'Tag',
                'validParents' => [
                    39109479,
                ],
            ],
            9152 => [
                'datatype' => 'container',
                'name' => 'Targets',
                'validParents' => [
                    13171,
                ],
            ],
            10442 => [
                'datatype' => 'uint',
                'name' => 'TargetTypeValue',
                'validParents' => [
                    9152,
                ],
            ],
            9162 => [
                'datatype' => 'string',
                'name' => 'TargetType',
                'validParents' => [
                    9152,
                ],
            ],
            9157 => [
                'datatype' => 'uint',
                'name' => 'TagTrackUID',
                'validParents' => [
                    9152,
                ],
            ],
            9161 => [
                'datatype' => 'uint',
                'name' => 'TagEditionUID',
                'validParents' => [
                    9152,
                ],
            ],
            9156 => [
                'datatype' => 'uint',
                'name' => 'TagChapterUID',
                'validParents' => [
                    9152,
                ],
            ],
            9158 => [
                'datatype' => 'uint',
                'name' => 'TagAttachmentUID',
                'validParents' => [
                    9152,
                ],
            ],
            10184 => [
                'datatype' => 'container',
                'name' => 'SimpleTag',
                'validParents' => [
                    13171,
                    10184,
                ],
            ],
            1443 => [
                'datatype' => 'string',
                'name' => 'TagName',
                'validParents' => [
                    10184,
                ],
            ],
            1146 => [
                'datatype' => 'string',
                'name' => 'TagLanguage',
                'validParents' => [
                    10184,
                ],
            ],
            1156 => [
                'datatype' => 'uint',
                'name' => 'TagDefault',
                'validParents' => [
                    10184,
                ],
            ],
            1159 => [
                'datatype' => 'string',
                'name' => 'TagString',
                'validParents' => [
                    10184,
                ],
            ],
            1157 => [
                'datatype' => 'binary',
                'name' => 'TagBinary',
                'validParents' => [
                    10184,
                ],
            ],
        ];
        public $ids = [
            'EBML' => 172351395,
            'EBMLVERSION' => 646,
            'EBMLREADVERSION' => 759,
            'EBMLMAXIDLENGTH' => 754,
            'EBMLMAXSIZELENGTH' => 755,
            'DOCTYPE' => 642,
            'DOCTYPEVERSION' => 647,
            'DOCTYPEREADVERSION' => 645,
            'VOID' => 108,
            'CRC-32' => 63,
            'SIGNATURESLOT' => 190023271,
            'SIGNATUREALGO' => 16010,
            'SIGNATUREHASH' => 16026,
            'SIGNATUREPUBLICKEY' => 16037,
            'SIGNATURE' => 16053,
            'SIGNATUREELEMENTS' => 15963,
            'SIGNATUREELEMENTLIST' => 15995,
            'SIGNEDELEMENT' => 9522,
            'SEGMENT' => 139690087,
            'SEEKHEAD' => 21863284,
            'SEEK' => 3515,
            'SEEKID' => 5035,
            'SEEKPOSITION' => 5036,
            'INFO' => 88713574,
            'SEGMENTUID' => 13220,
            'SEGMENTFILENAME' => 13188,
            'PREVUID' => 1882403,
            'PREVFILENAME' => 1868715,
            'NEXTUID' => 2013475,
            'NEXTFILENAME' => 1999803,
            'SEGMENTFAMILY' => 1092,
            'CHAPTERTRANSLATE' => 10532,
            'CHAPTERTRANSLATEEDITIONUID' => 10748,
            'CHAPTERTRANSLATECODEC' => 10687,
            'CHAPTERTRANSLATEID' => 10661,
            'TIMECODESCALE' => 710577,
            'DURATION' => 1161,
            'DATEUTC' => 1121,
            'TITLE' => 15273,
            'MUXINGAPP' => 3456,
            'WRITINGAPP' => 5953,
            'CLUSTER' => 256095861,
            'TIMECODE' => 103,
            'SILENTTRACKS' => 6228,
            'SILENTTRACKNUMBER' => 6359,
            'POSITION' => 39,
            'PREVSIZE' => 43,
            'SIMPLEBLOCK' => 35,
            'BLOCKGROUP' => 32,
            'BLOCK' => 33,
            'BLOCKVIRTUAL' => 34,
            'BLOCKADDITIONS' => 13729,
            'BLOCKMORE' => 38,
            'BLOCKADDID' => 110,
            'BLOCKADDITIONAL' => 37,
            'BLOCKDURATION' => 27,
            'REFERENCEPRIORITY' => 122,
            'REFERENCEBLOCK' => 123,
            'REFERENCEVIRTUAL' => 125,
            'CODECSTATE' => 36,
            'DISCARDPADDING' => 13730,
            'SLICES' => 14,
            'TIMESLICE' => 104,
            'LACENUMBER' => 76,
            'FRAMENUMBER' => 77,
            'BLOCKADDITIONID' => 75,
            'DELAY' => 78,
            'SLICEDURATION' => 79,
            'REFERENCEFRAME' => 72,
            'REFERENCEOFFSET' => 73,
            'REFERENCETIMECODE' => 74,
            'ENCRYPTEDBLOCK' => 47,
            'TRACKS' => 106212971,
            'TRACKENTRY' => 46,
            'TRACKNUMBER' => 87,
            'TRACKUID' => 13253,
            'TRACKTYPE' => 3,
            'FLAGENABLED' => 57,
            'FLAGDEFAULT' => 8,
            'FLAGFORCED' => 5546,
            'FLAGLACING' => 28,
            'MINCACHE' => 11751,
            'MAXCACHE' => 11768,
            'DEFAULTDURATION' => 254851,
            'DEFAULTDECODEDFIELDDURATION' => 216698,
            'TRACKTIMECODESCALE' => 209231,
            'TRACKOFFSET' => 4991,
            'MAXBLOCKADDITIONID' => 5614,
            'NAME' => 4974,
            'LANGUAGE' => 177564,
            'CODECID' => 6,
            'CODECPRIVATE' => 9122,
            'CODECNAME' => 362120,
            'ATTACHMENTLINK' => 13382,
            'CODECSETTINGS' => 1742487,
            'CODECINFOURL' => 1785920,
            'CODECDOWNLOADURL' => 438848,
            'CODECDECODEALL' => 42,
            'TRACKOVERLAY' => 12203,
            'CODECDELAY' => 5802,
            'SEEKPREROLL' => 5819,
            'TRACKTRANSLATE' => 9764,
            'TRACKTRANSLATEEDITIONUID' => 9980,
            'TRACKTRANSLATECODEC' => 9919,
            'TRACKTRANSLATETRACKID' => 9893,
            'VIDEO' => 96,
            'FLAGINTERLACED' => 26,
            'STEREOMODE' => 5048,
            'ALPHAMODE' => 5056,
            'OLDSTEREOMODE' => 5049,
            'PIXELWIDTH' => 48,
            'PIXELHEIGHT' => 58,
            'PIXELCROPBOTTOM' => 5290,
            'PIXELCROPTOP' => 5307,
            'PIXELCROPLEFT' => 5324,
            'PIXELCROPRIGHT' => 5341,
            'DISPLAYWIDTH' => 5296,
            'DISPLAYHEIGHT' => 5306,
            'DISPLAYUNIT' => 5298,
            'ASPECTRATIOTYPE' => 5299,
            'COLOURSPACE' => 963876,
            'GAMMAVALUE' => 1029411,
            'FRAMERATE' => 230371,
            'AUDIO' => 97,
            'SAMPLINGFREQUENCY' => 53,
            'OUTPUTSAMPLINGFREQUENCY' => 14517,
            'CHANNELS' => 31,
            'CHANNELPOSITIONS' => 15739,
            'BITDEPTH' => 8804,
            'TRACKOPERATION' => 98,
            'TRACKCOMBINEPLANES' => 99,
            'TRACKPLANE' => 100,
            'TRACKPLANEUID' => 101,
            'TRACKPLANETYPE' => 102,
            'TRACKJOINBLOCKS' => 105,
            'TRACKJOINUID' => 109,
            'TRICKTRACKUID' => 64,
            'TRICKTRACKSEGMENTUID' => 65,
            'TRICKTRACKFLAG' => 70,
            'TRICKMASTERTRACKUID' => 71,
            'TRICKMASTERTRACKSEGMENTUID' => 68,
            'CONTENTENCODINGS' => 11648,
            'CONTENTENCODING' => 8768,
            'CONTENTENCODINGORDER' => 4145,
            'CONTENTENCODINGSCOPE' => 4146,
            'CONTENTENCODINGTYPE' => 4147,
            'CONTENTCOMPRESSION' => 4148,
            'CONTENTCOMPALGO' => 596,
            'CONTENTCOMPSETTINGS' => 597,
            'CONTENTENCRYPTION' => 4149,
            'CONTENTENCALGO' => 2017,
            'CONTENTENCKEYID' => 2018,
            'CONTENTSIGNATURE' => 2019,
            'CONTENTSIGKEYID' => 2020,
            'CONTENTSIGALGO' => 2021,
            'CONTENTSIGHASHALGO' => 2022,
            'CUES' => 206814059,
            'CUEPOINT' => 59,
            'CUETIME' => 51,
            'CUETRACKPOSITIONS' => 55,
            'CUETRACK' => 119,
            'CUECLUSTERPOSITION' => 113,
            'CUERELATIVEPOSITION' => 112,
            'CUEDURATION' => 50,
            'CUEBLOCKNUMBER' => 4984,
            'CUECODECSTATE' => 106,
            'CUEREFERENCE' => 91,
            'CUEREFTIME' => 22,
            'CUEREFCLUSTER' => 23,
            'CUEREFNUMBER' => 4959,
            'CUEREFCODECSTATE' => 107,
            'ATTACHMENTS' => 155296873,
            'ATTACHEDFILE' => 8615,
            'FILEDESCRIPTION' => 1662,
            'FILENAME' => 1646,
            'FILEMIMETYPE' => 1632,
            'FILEDATA' => 1628,
            'FILEUID' => 1710,
            'FILEREFERRAL' => 1653,
            'FILEUSEDSTARTTIME' => 1633,
            'FILEUSEDENDTIME' => 1634,
            'CHAPTERS' => 4433776,
            'EDITIONENTRY' => 1465,
            'EDITIONUID' => 1468,
            'EDITIONFLAGHIDDEN' => 1469,
            'EDITIONFLAGDEFAULT' => 1499,
            'EDITIONFLAGORDERED' => 1501,
            'CHAPTERATOM' => 54,
            'CHAPTERUID' => 13252,
            'CHAPTERSTRINGUID' => 5716,
            'CHAPTERTIMESTART' => 17,
            'CHAPTERTIMEEND' => 18,
            'CHAPTERFLAGHIDDEN' => 24,
            'CHAPTERFLAGENABLED' => 1432,
            'CHAPTERSEGMENTUID' => 11879,
            'CHAPTERSEGMENTEDITIONUID' => 11964,
            'CHAPTERPHYSICALEQUIV' => 9155,
            'CHAPTERTRACK' => 15,
            'CHAPTERTRACKNUMBER' => 9,
            'CHAPTERDISPLAY' => 0,
            'CHAPSTRING' => 5,
            'CHAPLANGUAGE' => 892,
            'CHAPCOUNTRY' => 894,
            'CHAPPROCESS' => 10564,
            'CHAPPROCESSCODECID' => 10581,
            'CHAPPROCESSPRIVATE' => 1293,
            'CHAPPROCESSCOMMAND' => 10513,
            'CHAPPROCESSTIME' => 10530,
            'CHAPPROCESSDATA' => 10547,
            'TAGS' => 39109479,
            'TAG' => 13171,
            'TARGETS' => 9152,
            'TARGETTYPEVALUE' => 10442,
            'TARGETTYPE' => 9162,
            'TAGTRACKUID' => 9157,
            'TAGEDITIONUID' => 9161,
            'TAGCHAPTERUID' => 9156,
            'TAGATTACHMENTUID' => 9158,
            'SIMPLETAG' => 10184,
            'TAGNAME' => 1443,
            'TAGLANGUAGE' => 1146,
            'TAGDEFAULT' => 1156,
            'TAGSTRING' => 1159,
            'TAGBINARY' => 1157,
        ];
        public static $instance;

        public static function singleton()
        {
                self::$instance || self::$instance = new EBMLElements();
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
