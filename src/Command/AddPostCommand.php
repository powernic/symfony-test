<?php


namespace App\Command;

use App\Entity\News;
use App\Utils\Validator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * A console command that creates users and stores them in the database.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console app:add-post
 *
 * To output detailed information, increase the command verbosity:
 *
 *     $ php bin/console app:add-post -vv
 */
class AddPostCommand extends Command
{
    const MAX_ATTEMPTS = 5;

    private $io;
    private $entityManager;
    private $validator;

    public function __construct(EntityManagerInterface $em, Validator $validator)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            // a good practice is to use the 'app:' prefix to group all your custom application commands
            ->setName('app:add-post')
            ->setDescription('Creates news and stores them in the database')
            ->setHelp($this->getCommandHelp())
            ->addArgument('title', InputArgument::OPTIONAL, 'The title of the new post')
            ->addArgument('description', InputArgument::OPTIONAL, 'The text of the new post')
            ->addArgument('date', InputArgument::OPTIONAL, 'The date of the new post')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('title') && null !== $input->getArgument('description') && null !== $input->getArgument('date') ) {
            return;
        }

        $this->io->title('Add Post Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:add-post title description 2018-11-21',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the title if it's not defined
        $title = $input->getArgument('title');
        if (null !== $title) {
            $this->io->text(' > <info>Title</info>: '.str_repeat('*', mb_strlen($title)));
        } else {
            $title = $this->io->ask('Title', null, [$this->validator, 'validateTitle']);
            $input->setArgument('title', $title);
        }

        // Ask for the description if it's not defined
        $description = $input->getArgument('description');
        if (null !== $description) {
            $this->io->text(' > <info>Description</info>: '.$description);
        } else {
            $description = $this->io->ask('Description', null, [$this->validator, 'validateDescription']);
            $input->setArgument('description', $description);
        }

        // Ask for the full name if it's not defined
        $date = $input->getArgument('date');
        if (null !== $date) {
            $this->io->text(' > <info>Date (Y-m-d)</info>: '.$date);
        } else {
            $date = $this->io->ask('Date', null, [$this->validator, 'validateDate']);
            $input->setArgument('date', $date);
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $title = $input->getArgument('title');
        $description = $input->getArgument('description');
        $date = $input->getArgument('date');

        // make sure to validate the post data is correct
        $this->validatePostData($title, $description, $date);

        $date = DateTime::createFromFormat('Y-m-d', $date);
        // create the post
        $post = new News();
        $post->setName($title);
        $post->setDescription($description);
        $post->setDate($date);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $this->io->success(sprintf('New Post "%s" was successfully created: %s (%s)',   $post->getName(), $post->getDescription(), $post->getDate()));

        if ($output->isVerbose()) {
            $this->io->comment(sprintf('New post database id: %d', $post->getId()));
        }
    }

    private function validatePostData($title, $description, $date)
    {
        $newsRepository = $this->entityManager->getRepository(News::class);

        // first check if a user with the same username already exists.
        $existingPost = $newsRepository->findOneBy(['name' => $title]);

        if (null !== $existingPost) {
            throw new \RuntimeException(sprintf('There is already a post added with the "%s" title.', $title));
        }

        $this->validator->validateTitle($title);
        $this->validator->validateDescription($description);
        $this->validator->validateDate($date);

    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp()
    {
        return <<<'HELP'
The <info>%command.title%</info> command creates news and saves them in the database:

  <info>php %command.description%</info> <comment>description</comment>
  
  <info>php %command.date%</info> <comment>date</comment>

By default the command creates regular news.  
 
HELP;
    }
}
