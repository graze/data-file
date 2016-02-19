# data-file

<img align="right" src="http://media1.giphy.com/media/Z8d1CTOi4ola0/giphy.gif" width="300px" />

File manipulation

## Install

Via Composer

```bash
$ composer require graze/data-file
```

## Interfaces

- `FileModifierInterface` - Modify a single file
- `FileExpanderInterface` - Expand a single file into a collection of files
- `FileContractorInterface` - Contract a collection of files into a single file
- `FileTransferInterface` - Transfer a file to another file system

### Things

- Compression: `Gzip`,`Zip`
- Merging
- Generic File Transfer
- Convert Encoding
- Manipulation: `Replace Text`,`Head`,`Tail`
- Info: `FileInfo`

## Testing

```bash
$ make
$ make test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email harry.bragg@graze.com instead of using the issue tracker.

## Credits

- [Harry Bragg](https://github.com/h-bragg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
