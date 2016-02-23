# Change Log

All Notable changes to `data-file` will be documented in this file

## v1.0 - 2016-02-22

- Initial Release

### Added
- Finder: `MetadataFinder` - Will find files based on their metadata (size, created time, etc)
- Format: `CsvFormat` - Defines how a csv file is formatted
- Modify
  - Compression: `Gzip`, `Zip`, `FindCompression` - Compress, decompress and find the compression of files
  - Merge: `Merge` - Join multiple files into a single file
  - Transfer: `Transfer` - Transfer a file from any thephpleague/flysystem Filesystem to another filesystem
  - Encoding: `ConvertEncoding`, `FindEncoding` - Change or find the encoding of a file
  - Line manipulation: `Head`,`Tail` - Retrieve a segment of a file
  - Replace Text: `ReplaceText` - Replace all instances of a string with another string
